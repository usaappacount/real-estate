const express = require('express');
const QRCode = require('qrcode');
const pino = require('pino');
const fs = require('fs');
const path = require('path');
const crypto = require('crypto');

if (!globalThis.crypto) {
    globalThis.crypto = crypto.webcrypto;
}

const app = express();
app.use(express.json({ limit: '25mb' }));

const PORT = 3005;
const AUTH_DIR = path.join(process.cwd(), 'storage', 'app', 'wa_marketing_qr_auth');

let baileysModule = null;
let sock = null;
let activeSocketId = 0;
let lastStartTimestamp = 0;
let connectionStatus = 'disconnected'; // disconnected | qr_ready | connecting | connected
let currentQrBase64 = '';
let accountName = '';
let accountPhone = '';
let connectedAt = '';
let lastErrorMsg = '';
let reconnectTimer = null;
let versionAttempt = 0;

function clearReconnectTimer() {
    if (reconnectTimer) {
        clearTimeout(reconnectTimer);
        reconnectTimer = null;
    }
}

function clearAuthFolder() {
    try {
        if (fs.existsSync(AUTH_DIR)) {
            fs.rmSync(AUTH_DIR, { recursive: true, force: true });
        }
        fs.mkdirSync(AUTH_DIR, { recursive: true });
    } catch (e) {}
}

function safelyCloseCurrentSocket() {
    if (!sock) return;
    const oldSock = sock;
    sock = null;
    try { oldSock.end(undefined); } catch (e) {}
    try { oldSock.ws?.close(); } catch (e) {}
}

async function loadBaileys() {
    if (baileysModule) return baileysModule;
    try {
        baileysModule = require('@whiskeysockets/baileys');
    } catch (e) {
        baileysModule = await import('@whiskeysockets/baileys');
    }
    return baileysModule;
}

// جلب أحدث إصدار رسمي معتمد لواتساب ويب مع بدائل تلقائية
async function getLatestWaWebVersion(baileys) {
    const fallbackVersions = [
        [2, 3000, 1023202255],
        [2, 3000, 1021584919],
        [2, 3000, 1019707846]
    ];

    if (versionAttempt > 0) {
        return fallbackVersions[versionAttempt % fallbackVersions.length];
    }

    // 1. المحاولة عبر sw.js الرسمي لواتساب
    try {
        const controller = new AbortController();
        const timer = setTimeout(() => controller.abort(), 3000);
        const res = await fetch('https://web.whatsapp.com/sw.js', {
            headers: {
                'User-Agent': 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
                'Sec-Fetch-Dest': 'script',
                'Sec-Fetch-Mode': 'no-cors',
                'Sec-Fetch-Site': 'same-origin',
                'Accept': '*/*'
            },
            signal: controller.signal
        });
        clearTimeout(timer);
        if (res.ok) {
            const text = await res.text();
            const match = text.match(/"client_revision"\s*:\s*(\d+)/) || text.match(/2\.3000\.(\d+)/);
            if (match && match[1]) {
                return [2, 3000, parseInt(match[1], 10)];
            }
        }
    } catch (e) {}

    // 2. المحاولة عبر دالة Baileys المدمجة
    try {
        if (typeof baileys.fetchLatestWaWebVersion === 'function') {
            const waVer = await Promise.race([
                baileys.fetchLatestWaWebVersion({}),
                new Promise((_, reject) => setTimeout(() => reject(new Error('timeout')), 2500))
            ]);
            if (Array.isArray(waVer?.version) && waVer.version[2] > 1018000000) {
                return waVer.version;
            }
        }
    } catch (e) {}

    // 3. المحاولة عبر مستودع تحديثات واتساب المباشر
    try {
        const controller = new AbortController();
        const timer = setTimeout(() => controller.abort(), 2500);
        const res = await fetch('https://raw.githubusercontent.com/wppconnect-team/wa-version/main/versions.json', {
            signal: controller.signal
        });
        clearTimeout(timer);
        if (res.ok) {
            const text = await res.text();
            const matches = [...text.matchAll(/2\.3000\.(\d+)/g)];
            if (matches.length > 0) {
                const lastRev = parseInt(matches[matches.length - 1][1], 10);
                if (lastRev > 1018000000) {
                    return [2, 3000, lastRev];
                }
            }
        }
    } catch (e) {}

    return fallbackVersions[0];
}

async function startWhatsAppSocket(forceNewQr = false) {
    clearReconnectTimer();

    // إذا كان هناك باركود جاهز أو اتصال جاري حديثاً، لا نقطعه
    if (!forceNewQr && (connectionStatus === 'qr_ready' || connectionStatus === 'connected')) {
        return;
    }

    const mySocketId = ++activeSocketId;
    lastStartTimestamp = Date.now();

    try {
        const baileys = await loadBaileys();
        const makeWASocket = baileys.default?.default || baileys.default || baileys.makeWASocket;
        const useMultiFileAuthState = baileys.useMultiFileAuthState || baileys.default?.useMultiFileAuthState;
        const DisconnectReason = baileys.DisconnectReason || baileys.default?.DisconnectReason || {};
        const Browsers = baileys.Browsers || baileys.default?.Browsers;

        safelyCloseCurrentSocket();

        if (forceNewQr) {
            clearAuthFolder();
            currentQrBase64 = '';
            accountName = '';
            accountPhone = '';
        }

        if (!fs.existsSync(AUTH_DIR)) {
            fs.mkdirSync(AUTH_DIR, { recursive: true });
        }

        connectionStatus = 'connecting';
        lastErrorMsg = '';

        const { state, saveCreds } = await useMultiFileAuthState(AUTH_DIR);
        const version = await getLatestWaWebVersion(baileys);

        if (mySocketId !== activeSocketId) return;

        const mySock = makeWASocket({
            version,
            logger: pino({ level: 'silent' }),
            printQRInTerminal: false,
            auth: state,
            browser: Browsers ? Browsers.ubuntu('Chrome') : ['Ubuntu', 'Chrome', '20.0.04'],
            syncFullHistory: false,
            markOnlineOnConnect: true,
            connectTimeoutMs: 45000,
            defaultQueryTimeoutMs: 45000,
            keepAliveIntervalMs: 25000
        });

        sock = mySock;

        mySock.ev.on('creds.update', () => {
            if (mySocketId !== activeSocketId) return;
            saveCreds();
        });

        mySock.ev.on('connection.update', async (update) => {
            // تجاهل أي إشعارات قادمة من جلسة قديمة مغلقة
            if (mySocketId !== activeSocketId) return;

            const { connection, lastDisconnect, qr } = update;

            if (qr) {
                try {
                    currentQrBase64 = await QRCode.toDataURL(qr, { margin: 2, width: 280 });
                    connectionStatus = 'qr_ready';
                    lastErrorMsg = '';
                } catch (e) {
                    lastErrorMsg = e.message;
                }
            }

            if (connection === 'open') {
                versionAttempt = 0;
                connectionStatus = 'connected';
                currentQrBase64 = '';
                lastErrorMsg = '';
                const rawId = mySock.user?.id || '';
                accountPhone = rawId.split(':')[0].split('@')[0] || '';
                accountName = mySock.user?.name || mySock.user?.verifiedName || mySock.user?.notify || ('حساب واتساب (' + accountPhone + ')');
                connectedAt = new Date().toLocaleString('sv-SE');
            }

            if (connection === 'close') {
                const statusCode = lastDisconnect?.error?.output?.statusCode;
                const errReason = lastDisconnect?.error?.message || '';
                lastErrorMsg = `Code: ${statusCode || 'unknown'} ${errReason}`;

                // الكود 515 يعني نجاح مسح الباركود وطلب إعادة تشغيل الجلسة فوراً
                if (statusCode === DisconnectReason.restartRequired || statusCode === 515) {
                    connectionStatus = 'connecting';
                    currentQrBase64 = '';
                    reconnectTimer = setTimeout(() => startWhatsAppSocket(false), 200);
                    return;
                }

                // الكود 405 يعني رفض رقم الإصدار؛ ننتقل للإصدار البديل ونمسح المجلد فوراً
                if (statusCode === 405) {
                    versionAttempt++;
                    clearAuthFolder();
                    connectionStatus = 'connecting';
                    currentQrBase64 = '';
                    reconnectTimer = setTimeout(() => startWhatsAppSocket(false), 300);
                    return;
                }

                const isLoggedOut = statusCode === DisconnectReason.loggedOut || statusCode === 401 || statusCode === 403;

                connectionStatus = 'disconnected';
                currentQrBase64 = '';
                accountName = '';
                accountPhone = '';
                connectedAt = '';

                if (isLoggedOut) {
                    clearAuthFolder();
                    reconnectTimer = setTimeout(() => startWhatsAppSocket(false), 800);
                } else {
                    reconnectTimer = setTimeout(() => startWhatsAppSocket(false), 1500);
                }
            }
        });
    } catch (err) {
        lastErrorMsg = err.message || String(err);
        connectionStatus = 'disconnected';
        reconnectTimer = setTimeout(() => startWhatsAppSocket(false), 2000);
    }
}

app.get('/status', (req, res) => {
    res.json({
        connected: connectionStatus === 'connected',
        status: connectionStatus,
        qr_url: currentQrBase64,
        account_name: connectionStatus === 'connected' ? accountName : '',
        account_phone: connectionStatus === 'connected' ? accountPhone : '',
        connected_at: connectionStatus === 'connected' ? connectedAt : '',
        last_error: lastErrorMsg
    });
});

app.post('/init-qr', async (req, res) => {
    if (connectionStatus === 'connected') {
        return res.json({
            success: true,
            connected: true,
            status: 'connected',
            qr_url: '',
            account_name: accountName,
            account_phone: accountPhone
        });
    }

    if (currentQrBase64) {
        return res.json({
            success: true,
            connected: false,
            status: 'qr_ready',
            qr_url: currentQrBase64,
            account_name: '',
            account_phone: ''
        });
    }

    // إذا لم يكن في حالة اتصال نشطة بدأت قبل أقل من 8 ثوانٍ، نبدأ جلسة جديدة
    const isRecentlyConnecting = (connectionStatus === 'connecting') && ((Date.now() - lastStartTimestamp) < 8000);
    if (!isRecentlyConnecting) {
        await startWhatsAppSocket(true);
    }

    // انتظار حتى 4 ثوانٍ فقط لعدم تعليق طلب PHP، والفحص التلقائي سيتكفل بالباقي
    for (let i = 0; i < 20; i++) {
        if (currentQrBase64 || connectionStatus === 'connected') break;
        await new Promise(r => setTimeout(r, 200));
    }

    res.json({
        success: true,
        connected: connectionStatus === 'connected',
        status: currentQrBase64 ? 'qr_ready' : connectionStatus,
        qr_url: currentQrBase64,
        account_name: accountName,
        account_phone: accountPhone,
        last_error: lastErrorMsg
    });
});

app.post('/logout', async (req, res) => {
    clearReconnectTimer();
    activeSocketId++;
    try {
        if (sock) {
            await sock.logout();
        }
    } catch (e) {}
    safelyCloseCurrentSocket();
    clearAuthFolder();
    connectionStatus = 'disconnected';
    currentQrBase64 = '';
    accountName = '';
    accountPhone = '';
    connectedAt = '';
    res.json({ success: true, status: 'disconnected' });
});

app.post('/send', async (req, res) => {
    if (connectionStatus !== 'connected' || !sock) {
        return res.status(400).json({ success: false, message: 'لا يوجد حساب واتساب مرتبط حالياً عبر الـ QR.' });
    }

    try {
        const { to, message, media_data, media_filename, media_type } = req.body;
        let digits = String(to || '').replace(/\D/g, '');
        if (digits.startsWith('0')) digits = '966' + digits.slice(1);
        const jid = digits + '@s.whatsapp.net';

        if (media_data) {
            let buffer;
            if (media_data.includes('base64,')) {
                buffer = Buffer.from(media_data.split('base64,')[1], 'base64');
            } else if (media_data.startsWith('http')) {
                buffer = { url: media_data };
            } else {
                buffer = Buffer.from(media_data, 'base64');
            }

            if ((media_type || '').includes('image')) {
                await sock.sendMessage(jid, { image: buffer, caption: message || '' });
            } else if ((media_type || '').includes('video')) {
                await sock.sendMessage(jid, { video: buffer, caption: message || '' });
            } else if ((media_type || '').includes('audio')) {
                await sock.sendMessage(jid, { audio: buffer, mimetype: media_type });
            } else {
                await sock.sendMessage(jid, {
                    document: buffer,
                    mimetype: media_type || 'application/pdf',
                    fileName: media_filename || 'Document.pdf',
                    caption: message || ''
                });
            }
        } else {
            await sock.sendMessage(jid, { text: message || '' });
        }

        return res.json({ success: true, message: 'تم الإرسال بنجاح' });
    } catch (err) {
        return res.status(500).json({ success: false, message: err.message });
    }
});

app.listen(PORT, '127.0.0.1', () => {
    startWhatsAppSocket(false);
});
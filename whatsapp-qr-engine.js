const express = require('express');
const QRCode = require('qrcode');
const pino = require('pino');
const fs = require('fs');
const path = require('path');

const app = express();
app.use(express.json({ limit: '25mb' }));

const PORT = 3005;
const AUTH_DIR = path.join(__dirname, 'storage', 'app', 'wa_marketing_qr_auth');

let baileysModule = null;
let sock = null;
let connectionStatus = 'disconnected'; // disconnected | qr_ready | connecting | connected
let currentQrBase64 = '';
let accountName = '';
let accountPhone = '';
let connectedAt = '';
let isInitializing = false;
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
    } catch (e) {}
}

// تحميل مكتبة Baileys بدعم كامل لنظامي CommonJS و ESM في Node 20
async function loadBaileys() {
    if (baileysModule) return baileysModule;
    try {
        baileysModule = require('@whiskeysockets/baileys');
    } catch (e) {
        baileysModule = await import('@whiskeysockets/baileys');
    }
    return baileysModule;
}

// جلب أحدث رقم إصدار رسمي لواتساب ويب مع فحص رقم المراجعة الفعلي (Index 2)
async function getLatestWaWebVersion(baileys) {
    const fallbackVersions = [
        [2, 3000, 1025190524],
        [2, 3000, 1024180211],
        [2, 3000, 1023202255]
    ];

    if (versionAttempt > 0) {
        return fallbackVersions[versionAttempt % fallbackVersions.length];
    }

    const headers = {
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
        'Accept': '*/*',
        'Sec-Fetch-Dest': 'script',
        'Sec-Fetch-Mode': 'no-cors',
        'Sec-Fetch-Site': 'same-origin'
    };

    try {
        if (typeof baileys.fetchLatestWaWebVersion === 'function') {
            const waVer = await Promise.race([
                baileys.fetchLatestWaWebVersion({ headers }),
                new Promise((_, reject) => setTimeout(() => reject(new Error('timeout')), 3000))
            ]);
            if (Array.isArray(waVer?.version) && waVer.version[2] >= 1020000000) {
                return waVer.version;
            }
        }
    } catch (e) {}

    for (const url of ['https://web.whatsapp.com/sw.js', 'https://web.whatsapp.com/']) {
        try {
            const controller = new AbortController();
            const timer = setTimeout(() => controller.abort(), 3500);
            const res = await fetch(url, { headers, signal: controller.signal });
            clearTimeout(timer);
            if (res.ok) {
                const text = await res.text();
                const match = text.match(/(?:client_revision["']?\s*:\s*|2\.3000\.)(\d{9,11})/);
                if (match && match[1]) {
                    const rev = parseInt(match[1], 10);
                    if (rev >= 1020000000) {
                        return [2, 3000, rev];
                    }
                }
            }
        } catch (e) {}
    }

    try {
        if (typeof baileys.fetchLatestBaileysVersion === 'function') {
            const vRes = await Promise.race([
                baileys.fetchLatestBaileysVersion(),
                new Promise((_, reject) => setTimeout(() => reject(new Error('timeout')), 2500))
            ]);
            // فحص الخانة الثالثة [2] وليس الثانية [1] لتجنب الإصدارات القديمة المرفوضة
            if (Array.isArray(vRes?.version) && vRes.version[2] >= 1023000000) {
                return vRes.version;
            }
        }
    } catch (e) {}

    return fallbackVersions[0];
}

async function startWhatsAppSocket(forceNewQr = false) {
    if (isInitializing && !forceNewQr) return;
    isInitializing = true;
    clearReconnectTimer();

    try {
        const baileys = await loadBaileys();
        const makeWASocket = baileys.default?.default || baileys.default || baileys.makeWASocket;
        const useMultiFileAuthState = baileys.useMultiFileAuthState || baileys.default?.useMultiFileAuthState;
        const DisconnectReason = baileys.DisconnectReason || baileys.default?.DisconnectReason || {};
        const Browsers = baileys.Browsers || baileys.default?.Browsers;

        if (forceNewQr) {
            if (sock) {
                try { sock.ev.removeAllListeners(); sock.end(); } catch (e) {}
                sock = null;
            }
            clearAuthFolder();
            connectionStatus = 'connecting';
            currentQrBase64 = '';
            accountName = '';
            accountPhone = '';
        }

        if (!fs.existsSync(AUTH_DIR)) {
            fs.mkdirSync(AUTH_DIR, { recursive: true });
        }

        const { state, saveCreds } = await useMultiFileAuthState(AUTH_DIR);
        const version = await getLatestWaWebVersion(baileys);

        sock = makeWASocket({
            version,
            logger: pino({ level: 'silent' }),
            printQRInTerminal: false,
            auth: state,
            browser: Browsers ? Browsers.ubuntu('Chrome') : ['Ubuntu', 'Chrome', '20.0.04'],
            syncFullHistory: false,
            markOnlineOnConnect: true,
            connectTimeoutMs: 60000,
            defaultQueryTimeoutMs: 60000,
            keepAliveIntervalMs: 25000
        });

        sock.ev.on('creds.update', saveCreds);

        sock.ev.on('connection.update', async (update) => {
            const { connection, lastDisconnect, qr } = update;

            if (qr) {
                try {
                    currentQrBase64 = await QRCode.toDataURL(qr, { margin: 2, width: 280 });
                    connectionStatus = 'qr_ready';
                } catch (e) {}
            }

            if (connection === 'open') {
                versionAttempt = 0;
                connectionStatus = 'connected';
                currentQrBase64 = '';
                const rawId = sock.user?.id || '';
                accountPhone = rawId.split(':')[0].split('@')[0] || '';
                accountName = sock.user?.name || sock.user?.verifiedName || sock.user?.notify || ('حساب واتساب (' + accountPhone + ')');
                connectedAt = new Date().toLocaleString('sv-SE');
            }

            if (connection === 'close') {
                const statusCode = lastDisconnect?.error?.output?.statusCode;

                if (statusCode === DisconnectReason.restartRequired || statusCode === 515) {
                    connectionStatus = 'connecting';
                    currentQrBase64 = '';
                    clearReconnectTimer();
                    reconnectTimer = setTimeout(() => startWhatsAppSocket(false), 200);
                    return;
                }

                // في حال رفض الإصدار بكود 405 نجرب الإصدار البديل فوراً لتوليد الباركود
                if (statusCode === 405) {
                    versionAttempt++;
                    clearAuthFolder();
                    connectionStatus = 'connecting';
                    currentQrBase64 = '';
                    clearReconnectTimer();
                    reconnectTimer = setTimeout(() => startWhatsAppSocket(false), 300);
                    return;
                }

                const isLoggedOut = statusCode === DisconnectReason.loggedOut || statusCode === 401 || statusCode === 403;

                connectionStatus = 'disconnected';
                currentQrBase64 = '';
                accountName = '';
                accountPhone = '';
                connectedAt = '';

                clearReconnectTimer();
                if (isLoggedOut) {
                    clearAuthFolder();
                    reconnectTimer = setTimeout(() => startWhatsAppSocket(false), 1200);
                } else {
                    reconnectTimer = setTimeout(() => startWhatsAppSocket(false), 1800);
                }
            }
        });
    } catch (err) {
        connectionStatus = 'disconnected';
        clearReconnectTimer();
        reconnectTimer = setTimeout(() => startWhatsAppSocket(false), 2500);
    } finally {
        isInitializing = false;
    }
}

app.get('/status', (req, res) => {
    res.json({
        connected: connectionStatus === 'connected',
        status: connectionStatus,
        qr_url: currentQrBase64,
        account_name: connectionStatus === 'connected' ? accountName : '',
        account_phone: connectionStatus === 'connected' ? accountPhone : '',
        connected_at: connectionStatus === 'connected' ? connectedAt : ''
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

    if (currentQrBase64 && connectionStatus === 'qr_ready') {
        return res.json({
            success: true,
            connected: false,
            status: 'qr_ready',
            qr_url: currentQrBase64,
            account_name: '',
            account_phone: ''
        });
    }

    await startWhatsAppSocket(true);

    for (let i = 0; i < 65; i++) {
        if (currentQrBase64 || connectionStatus === 'connected') break;
        await new Promise(r => setTimeout(r, 200));
    }

    res.json({
        success: true,
        connected: connectionStatus === 'connected',
        status: currentQrBase64 ? 'qr_ready' : (connectionStatus === 'connected' ? 'connected' : 'connecting'),
        qr_url: currentQrBase64,
        account_name: accountName,
        account_phone: accountPhone
    });
});

app.post('/logout', async (req, res) => {
    clearReconnectTimer();
    try {
        if (sock) {
            await sock.logout();
            sock.end();
        }
    } catch (e) {}
    sock = null;
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

// فتح المنفذ فوراً قبل بدء اتصال السوكيت لضمان استجابة المحرك دائماً
app.listen(PORT, '127.0.0.1', () => {
    startWhatsAppSocket(false);
});
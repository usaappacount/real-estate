const express = require('express');
const QRCode = require('qrcode');
const pino = require('pino');
const fs = require('fs');
const path = require('path');
const baileys = require('@whiskeysockets/baileys');

const makeWASocket = baileys.default?.default || baileys.default || baileys.makeWASocket;
const {
    useMultiFileAuthState,
    DisconnectReason,
    fetchLatestBaileysVersion,
    Browsers
} = baileys;

const app = express();
app.use(express.json({ limit: '25mb' }));

const PORT = 3005;
const AUTH_DIR = path.join(__dirname, 'storage', 'app', 'wa_marketing_qr_auth');

let sock = null;
let connectionStatus = 'disconnected'; // disconnected | qr_ready | connecting | connected
let currentQrBase64 = '';
let accountName = '';
let accountPhone = '';
let connectedAt = '';
let isInitializing = false;

function clearAuthFolder() {
    try {
        if (fs.existsSync(AUTH_DIR)) {
            fs.rmSync(AUTH_DIR, { recursive: true, force: true });
        }
    } catch (e) {}
}

// جلب أحدث رقم إصدار رسمي لواتساب ويب لعام 2026 لضمان قبول الربط من الجوال فوراً
async function getLatestWaWebVersion() {
    try {
        const controller = new AbortController();
        const timer = setTimeout(() => controller.abort(), 3500);
        const res = await fetch('https://web.whatsapp.com/sw.js', {
            headers: {
                'User-Agent': 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36'
            },
            signal: controller.signal
        });
        clearTimeout(timer);
        if (res.ok) {
            const text = await res.text();
            const match = text.match(/"client_revision"\s*:\s*(\d+)/);
            if (match && match[1]) {
                return [2, 3000, parseInt(match[1], 10)];
            }
        }
    } catch (e) {}

    try {
        const vRes = await Promise.race([
            fetchLatestBaileysVersion(),
            new Promise((_, reject) => setTimeout(() => reject(new Error('timeout')), 2500))
        ]);
        if (Array.isArray(vRes?.version) && vRes.version[1] >= 3000) {
            return vRes.version;
        }
    } catch (e) {}

    return [2, 3000, 1023202255];
}

async function startWhatsAppSocket(forceNewQr = false) {
    if (isInitializing && !forceNewQr) return;
    isInitializing = true;

    try {
        if (forceNewQr) {
            if (sock) {
                try { sock.ev.removeAllListeners(); sock.end(); } catch (e) {}
                sock = null;
            }
            clearAuthFolder();
            connectionStatus = 'disconnected';
            currentQrBase64 = '';
            accountName = '';
            accountPhone = '';
        }

        if (!fs.existsSync(AUTH_DIR)) {
            fs.mkdirSync(AUTH_DIR, { recursive: true });
        }

        const { state, saveCreds } = await useMultiFileAuthState(AUTH_DIR);
        const version = await getLatestWaWebVersion();

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
                connectionStatus = 'qr_ready';
                currentQrBase64 = await QRCode.toDataURL(qr, { margin: 2, width: 280 });
            }

            if (connection === 'open') {
                connectionStatus = 'connected';
                currentQrBase64 = '';
                const rawId = sock.user?.id || '';
                accountPhone = rawId.split(':')[0].split('@')[0] || '';
                accountName = sock.user?.name || sock.user?.verifiedName || sock.user?.notify || ('حساب واتساب (' + accountPhone + ')');
                connectedAt = new Date().toLocaleString('sv-SE');
            }

            if (connection === 'close') {
                const statusCode = lastDisconnect?.error?.output?.statusCode;

                // الكود 515 يعني أن الجوال مسح الباركود بنجاح ويطلب إعادة اتصال فورية لإتمام الربط
                if (statusCode === DisconnectReason.restartRequired || statusCode === 515) {
                    connectionStatus = 'connecting';
                    currentQrBase64 = '';
                    setTimeout(() => startWhatsAppSocket(false), 150);
                    return;
                }

                const isLoggedOut = statusCode === DisconnectReason.loggedOut || statusCode === 401 || statusCode === 403 || statusCode === 405;

                connectionStatus = 'disconnected';
                currentQrBase64 = '';
                accountName = '';
                accountPhone = '';
                connectedAt = '';

                if (isLoggedOut) {
                    clearAuthFolder();
                    setTimeout(() => startWhatsAppSocket(false), 1500);
                } else {
                    setTimeout(() => startWhatsAppSocket(false), 2000);
                }
            }
        });
    } catch (err) {
        connectionStatus = 'disconnected';
    } finally {
        isInitializing = false;
    }
}

// فحص الحالة الحقيقية
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

// طلب توليد كود QR حقيقي جديد
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

    // إذا كان هناك باركود حقيقي جاهز حالياً لا نمسحه حتى لا يفشل المسح من الجوال
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

    // انتظار توليد الباركود من خوادم واتساب (حتى 12 ثانية)
    for (let i = 0; i < 60; i++) {
        if (currentQrBase64 || connectionStatus === 'connected') break;
        await new Promise(r => setTimeout(r, 200));
    }

    res.json({
        success: Boolean(currentQrBase64 || connectionStatus === 'connected'),
        connected: connectionStatus === 'connected',
        status: connectionStatus,
        qr_url: currentQrBase64,
        account_name: accountName,
        account_phone: accountPhone
    });
});

// فصل الحساب المرتبط وتسجيل الخروج
app.post('/logout', async (req, res) => {
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

// إرسال رسالة إعلانية (نص أو مرفق صورة/PDF/فيديو) عبر الحساب المرتبط
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
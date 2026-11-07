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
    fetchLatestBaileysVersion
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

async function startWhatsAppSocket(forceNewQr = false) {
    if (isInitializing && !forceNewQr) return;
    isInitializing = true;

    try {
        if (forceNewQr) {
            if (sock) {
                try { sock.end(); } catch (e) {}
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

        let version = undefined;
        try {
            const vRes = await Promise.race([
                fetchLatestBaileysVersion(),
                new Promise((_, reject) => setTimeout(() => reject(new Error('timeout')), 2500))
            ]);
            version = vRes?.version;
        } catch (e) {}

        const socketConfig = {
            logger: pino({ level: 'silent' }),
            printQRInTerminal: false,
            auth: state,
            browser: ['RealEstate ERP Marketing', 'Chrome', '120.0.0']
        };
        if (version) {
            socketConfig.version = version;
        }

        sock = makeWASocket(socketConfig);

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
                const shouldReconnect = statusCode !== DisconnectReason.loggedOut;

                connectionStatus = 'disconnected';
                currentQrBase64 = '';
                accountName = '';
                accountPhone = '';
                connectedAt = '';

                if (!shouldReconnect) {
                    clearAuthFolder();
                } else {
                    setTimeout(() => startWhatsAppSocket(false), 3000);
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

    if (!sock && !isInitializing) {
        await startWhatsAppSocket(true);
    }

    // انتظار توليد الـ QR (حتى 10 ثوانٍ)
    for (let i = 0; i < 50; i++) {
        if (currentQrBase64 || connectionStatus === 'connected') break;
        await new Promise(r => setTimeout(r, 200));
    }

    // إذا لم يتولد بسبب جلسة قديمة معلقة، نعيد تهيئته من الصفر
    if (!currentQrBase64 && connectionStatus !== 'connected') {
        await startWhatsAppSocket(true);
        for (let i = 0; i < 40; i++) {
            if (currentQrBase64 || connectionStatus === 'connected') break;
            await new Promise(r => setTimeout(r, 200));
        }
    }

    res.json({
        success: true,
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
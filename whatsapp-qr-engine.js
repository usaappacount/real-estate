process.on('uncaughtException', (err) => {
    console.error('Uncaught Exception:', err);
});
process.on('unhandledRejection', (err) => {
    console.error('Unhandled Rejection:', err);
});

(async () => {
    const httpMod = await import('node:http');
    const fsMod = await import('node:fs');
    const pathMod = await import('node:path');
    const cryptoMod = await import('node:crypto');
    const childProcMod = await import('node:child_process');
    const moduleMod = await import('node:module');

    const http = httpMod.default || httpMod;
    const fs = fsMod.default || fsMod;
    const path = pathMod.default || pathMod;
    const crypto = cryptoMod.default || cryptoMod;
    const { execSync } = childProcMod.default || childProcMod;
    const { createRequire } = moduleMod.default || moduleMod;

    if (!globalThis.crypto) {
        globalThis.crypto = crypto.webcrypto || crypto;
    }

    const customRequire = createRequire(path.join(process.cwd(), 'package.json'));

    const PORT = 3005;
    const AUTH_DIR = path.join(process.cwd(), 'storage', 'app', 'wa_marketing_qr_auth');

    let baileysModule = null;
    let qrCodeModule = null;
    let pinoModule = null;

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

    function createSilentLogger() {
        if (pinoModule && typeof pinoModule === 'function') {
            try { return pinoModule({ level: 'silent' }); } catch (e) {}
        }
        const noop = () => {};
        const fallbackLogger = {
            level: 'silent',
            info: noop, error: noop, warn: noop, debug: noop, trace: noop, fatal: noop,
            child: () => fallbackLogger
        };
        return fallbackLogger;
    }

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

    async function loadDependencies() {
        if (baileysModule) return baileysModule;

        try {
            try {
                baileysModule = customRequire('@whiskeysockets/baileys');
            } catch (e1) {
                baileysModule = await import('@whiskeysockets/baileys');
            }
        } catch (err) {
            lastErrorMsg = 'جاري تجهيز مكتبة واتساب لأول مرة...';
            try {
                execSync('npm install @whiskeysockets/baileys@6.7.9 qrcode pino --no-save --no-audit --no-fund', {
                    cwd: process.cwd(),
                    stdio: 'ignore',
                    timeout: 120000
                });
                try {
                    baileysModule = customRequire('@whiskeysockets/baileys');
                } catch (e2) {
                    baileysModule = await import('@whiskeysockets/baileys');
                }
            } catch (installErr) {
                lastErrorMsg = 'تعذر تحميل مكتبة Baileys: ' + (installErr.message || String(installErr));
                throw installErr;
            }
        }

        try {
            qrCodeModule = customRequire('qrcode');
        } catch (e) {
            try {
                const q = await import('qrcode');
                qrCodeModule = q.default || q;
            } catch (e2) {}
        }

        try {
            const p = customRequire('pino');
            pinoModule = p.default || p;
        } catch (e) {
            try {
                const p = await import('pino');
                pinoModule = p.default || p;
            } catch (e2) {}
        }

        return baileysModule;
    }

    async function qrStringToImage(qrText) {
        if (qrCodeModule && typeof qrCodeModule.toDataURL === 'function') {
            try {
                return await qrCodeModule.toDataURL(qrText, { margin: 2, width: 280 });
            } catch (e) {}
        }
        return 'https://api.qrserver.com/v1/create-qr-code/?size=280x280&margin=10&data=' + encodeURIComponent(qrText);
    }

    async function resolveWaWebVersion(b) {
        const mode = versionAttempt % 3;

        if (mode === 0) {
            try {
                const controller = new AbortController();
                const timer = setTimeout(() => controller.abort(), 3000);
                const res = await fetch('https://web.whatsapp.com/sw.js', {
                    headers: {
                        'User-Agent': 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36'
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
        }

        if (mode === 1) {
            try {
                if (typeof b.fetchLatestWaWebVersion === 'function') {
                    const vRes = await Promise.race([
                        b.fetchLatestWaWebVersion({}),
                        new Promise((_, reject) => setTimeout(() => reject(new Error('timeout')), 2500))
                    ]);
                    if (Array.isArray(vRes?.version)) return vRes.version;
                }
            } catch (e) {}
        }

        return undefined; // استخدام الإصدار الافتراضي المدمج في Baileys
    }

    async function startWhatsAppSocket(forceNewQr = false) {
        clearReconnectTimer();

        if (!forceNewQr && (connectionStatus === 'qr_ready' || connectionStatus === 'connected')) {
            return;
        }

        const mySocketId = ++activeSocketId;
        lastStartTimestamp = Date.now();
        connectionStatus = 'connecting';

        try {
            const rawBaileys = await loadDependencies();
            if (mySocketId !== activeSocketId) return;

            const b = rawBaileys.makeWASocket ? rawBaileys : (rawBaileys.default?.makeWASocket ? rawBaileys.default : rawBaileys);
            const makeWASocket = b.makeWASocket || (typeof b.default === 'function' ? b.default : null) || (typeof rawBaileys.default === 'function' ? rawBaileys.default : null);
            const useMultiFileAuthState = b.useMultiFileAuthState || rawBaileys.useMultiFileAuthState;
            const DisconnectReason = b.DisconnectReason || rawBaileys.DisconnectReason || {};
            const Browsers = b.Browsers || rawBaileys.Browsers;

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

            const { state, saveCreds } = await useMultiFileAuthState(AUTH_DIR);
            const version = await resolveWaWebVersion(b);

            if (mySocketId !== activeSocketId) return;

            const socketConfig = {
                logger: createSilentLogger(),
                printQRInTerminal: false,
                auth: state,
                browser: Browsers ? Browsers.ubuntu('Chrome') : ['Ubuntu', 'Chrome', '20.0.04'],
                syncFullHistory: false,
                markOnlineOnConnect: true,
                connectTimeoutMs: 45000,
                defaultQueryTimeoutMs: 45000,
                keepAliveIntervalMs: 25000
            };
            if (Array.isArray(version)) {
                socketConfig.version = version;
            }

            const mySock = makeWASocket(socketConfig);
            sock = mySock;

            mySock.ev.on('creds.update', () => {
                if (mySocketId !== activeSocketId) return;
                saveCreds();
            });

            mySock.ev.on('connection.update', async (update) => {
                if (mySocketId !== activeSocketId) return;

                const { connection, lastDisconnect, qr } = update;

                if (qr) {
                    currentQrBase64 = await qrStringToImage(qr);
                    connectionStatus = 'qr_ready';
                    lastErrorMsg = '';
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
                    lastErrorMsg = `Code: ${statusCode || 'closed'} ${errReason}`;

                    if (statusCode === DisconnectReason.restartRequired || statusCode === 515) {
                        connectionStatus = 'connecting';
                        currentQrBase64 = '';
                        reconnectTimer = setTimeout(() => startWhatsAppSocket(false), 200);
                        return;
                    }

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
            reconnectTimer = setTimeout(() => startWhatsAppSocket(false), 2500);
        }
    }

    function sendJson(res, statusCode, payload) {
        const body = JSON.stringify(payload);
        res.writeHead(statusCode, {
            'Content-Type': 'application/json; charset=utf-8',
            'Content-Length': Buffer.byteLength(body)
        });
        res.end(body);
    }

    function readJsonBody(req) {
        return new Promise((resolve) => {
            let raw = '';
            req.on('data', (chunk) => { raw += chunk; });
            req.on('end', () => {
                try { resolve(raw ? JSON.parse(raw) : {}); }
                catch (e) { resolve({}); }
            });
            req.on('error', () => resolve({}));
        });
    }

    // تشغيل خادم HTTP المدمج فوراً على المنفذ 3005 بدون أي اعتماد خارجي
    const server = http.createServer(async (req, res) => {
        const url = (req.url || '/').split('?')[0];

        if (req.method === 'GET' && url === '/status') {
            return sendJson(res, 200, {
                engine_up: true,
                connected: connectionStatus === 'connected',
                status: connectionStatus,
                qr_url: currentQrBase64,
                account_name: connectionStatus === 'connected' ? accountName : '',
                account_phone: connectionStatus === 'connected' ? accountPhone : '',
                connected_at: connectionStatus === 'connected' ? connectedAt : '',
                last_error: lastErrorMsg
            });
        }

        if (req.method === 'POST' && url === '/init-qr') {
            if (connectionStatus === 'connected') {
                return sendJson(res, 200, {
                    success: true,
                    connected: true,
                    status: 'connected',
                    qr_url: '',
                    account_name: accountName,
                    account_phone: accountPhone
                });
            }

            if (currentQrBase64) {
                return sendJson(res, 200, {
                    success: true,
                    connected: false,
                    status: 'qr_ready',
                    qr_url: currentQrBase64,
                    account_name: '',
                    account_phone: ''
                });
            }

            const isRecentlyConnecting = (connectionStatus === 'connecting') && ((Date.now() - lastStartTimestamp) < 8000);
            if (!isRecentlyConnecting) {
                startWhatsAppSocket(true);
            }

            for (let i = 0; i < 20; i++) {
                if (currentQrBase64 || connectionStatus === 'connected') break;
                await new Promise(r => setTimeout(r, 200));
            }

            return sendJson(res, 200, {
                success: true,
                connected: connectionStatus === 'connected',
                status: currentQrBase64 ? 'qr_ready' : connectionStatus,
                qr_url: currentQrBase64,
                account_name: accountName,
                account_phone: accountPhone,
                last_error: lastErrorMsg
            });
        }

        if (req.method === 'POST' && url === '/logout') {
            clearReconnectTimer();
            activeSocketId++;
            try { if (sock) await sock.logout(); } catch (e) {}
            safelyCloseCurrentSocket();
            clearAuthFolder();
            connectionStatus = 'disconnected';
            currentQrBase64 = '';
            accountName = '';
            accountPhone = '';
            connectedAt = '';
            return sendJson(res, 200, { success: true, status: 'disconnected' });
        }

        if (req.method === 'POST' && url === '/send') {
            if (connectionStatus !== 'connected' || !sock) {
                return sendJson(res, 400, { success: false, message: 'لا يوجد حساب واتساب مرتبط حالياً عبر الـ QR.' });
            }

            try {
                const body = await readJsonBody(req);
                const { to, message, media_data, media_filename, media_type } = body;
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

                return sendJson(res, 200, { success: true, message: 'تم الإرسال بنجاح' });
            } catch (err) {
                return sendJson(res, 500, { success: false, message: err.message || String(err) });
            }
        }

        return sendJson(res, 404, { error: 'Not Found' });
    });

    server.on('error', (err) => {
        if (err && err.code === 'EADDRINUSE') {
            process.exit(0);
        }
    });

    server.listen(PORT, '127.0.0.1', () => {
        startWhatsAppSocket(false);
    });
})();
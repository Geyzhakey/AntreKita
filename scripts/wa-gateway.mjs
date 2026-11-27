import http from 'node:http';
import fs from 'node:fs';
import path from 'node:path';
import QRCode from 'qrcode';
import pino from 'pino';
import makeWASocket, {
    DisconnectReason,
    fetchLatestBaileysVersion,
    useMultiFileAuthState,
} from '@whiskeysockets/baileys';

const PORT = Number(process.env.WA_GATEWAY_PORT || 3001);
const AUTH_DIR = path.resolve(process.cwd(), 'storage/app/wa-auth');

const state = {
    connected: false,
    status: 'starting',
    qr: null,
    phone: null,
    pairingCode: null,
    lastError: null,
};

let sock = null;
let starting = false;

function normalizePhone(input) {
    let digits = String(input || '').replace(/[^\d]/g, '');
    if (digits.startsWith('0')) {
        digits = '62' + digits.slice(1);
    }
    return digits;
}

async function startSock() {
    if (starting) return;
    starting = true;

    try {
        fs.mkdirSync(AUTH_DIR, { recursive: true });

        const { state: authState, saveCreds } = await useMultiFileAuthState(AUTH_DIR);
        const { version } = await fetchLatestBaileysVersion();

        state.status = 'connecting';
        state.lastError = null;

        sock = makeWASocket({
            version,
            auth: authState,
            logger: pino({ level: 'silent' }),
            browser: ['AntreKita', 'Chrome', '120.0.0'],
            printQRInTerminal: false,
            syncFullHistory: false,
        });

        sock.ev.on('creds.update', saveCreds);

        sock.ev.on('connection.update', async (update) => {
            const { connection, lastDisconnect, qr } = update;

            if (qr) {
                try {
                    state.qr = await QRCode.toDataURL(qr, { margin: 1, width: 220 });
                    state.status = 'qr_ready';
                    state.connected = false;
                } catch (err) {
                    state.lastError = err?.message || 'Gagal membuat QR code.';
                }
            }

            if (connection === 'open') {
                state.connected = true;
                state.status = 'connected';
                state.qr = null;
                state.pairingCode = null;
                state.lastError = null;
                state.phone = sock.user?.id?.split(':')[0] || 'Linked';
            }

            if (connection === 'close') {
                state.connected = false;
                const statusCode = lastDisconnect?.error?.output?.statusCode;
                const loggedOut = statusCode === DisconnectReason.loggedOut;

                if (loggedOut) {
                    state.status = 'logged_out';
                    state.qr = null;
                    state.phone = null;
                    try {
                        fs.rmSync(AUTH_DIR, { recursive: true, force: true });
                    } catch {}
                    setTimeout(() => startSock(), 1000);
                } else {
                    state.status = 'reconnecting';
                    state.lastError = lastDisconnect?.error?.message || 'Koneksi terputus.';
                    setTimeout(() => startSock(), 2000);
                }
            }
        });
    } catch (err) {
        state.connected = false;
        state.status = 'error';
        state.lastError = err?.message || 'Gagal memulai koneksi WhatsApp.';
        setTimeout(() => startSock(), 4000);
    } finally {
        starting = false;
    }
}

function readJsonBody(req) {
    return new Promise((resolve, reject) => {
        let raw = '';
        req.on('data', (chunk) => {
            raw += chunk;
            if (raw.length > 1024 * 1024) {
                reject(new Error('Payload terlalu besar'));
            }
        });
        req.on('end', () => {
            try {
                resolve(raw ? JSON.parse(raw) : {});
            } catch {
                reject(new Error('JSON tidak valid'));
            }
        });
        req.on('error', reject);
    });
}

function sendJson(res, statusCode, payload) {
    res.writeHead(statusCode, { 'Content-Type': 'application/json; charset=utf-8' });
    res.end(JSON.stringify(payload));
}

const server = http.createServer(async (req, res) => {
    const url = (req.url || '/').split('?')[0];

    if (req.method === 'GET' && url === '/status') {
        return sendJson(res, 200, state);
    }

    if (req.method === 'POST' && url === '/pair') {
        try {
            const body = await readJsonBody(req);
            const digits = normalizePhone(body.phone);
            if (!digits || digits.length < 8) {
                return sendJson(res, 422, { ok: false, reason: 'Nomor telepon tidak valid.' });
            }
            if (!sock) {
                return sendJson(res, 503, { ok: false, reason: 'Socket WhatsApp belum siap.' });
            }
            const code = await sock.requestPairingCode(digits);
            state.pairingCode = code;
            return sendJson(res, 200, { ok: true, pairingCode: code });
        } catch (err) {
            return sendJson(res, 500, { ok: false, reason: err?.message || 'Gagal meminta kode tautan.' });
        }
    }

    if (req.method === 'POST' && url === '/send') {
        try {
            if (!state.connected || !sock) {
                return sendJson(res, 503, { status: false, reason: 'WhatsApp belum ditautkan.' });
            }

            const body = await readJsonBody(req);
            const digits = normalizePhone(body.to);
            const message = String(body.message || '').trim();

            if (!digits || !message) {
                return sendJson(res, 422, { status: false, reason: 'Nomor tujuan atau pesan kosong.' });
            }

            await sock.sendMessage(`${digits}@s.whatsapp.net`, { text: message });
            return sendJson(res, 200, { status: true });
        } catch (err) {
            return sendJson(res, 500, { status: false, reason: err?.message || 'Gagal mengirim pesan WhatsApp.' });
        }
    }

    if (req.method === 'POST' && url === '/reset') {
        try {
            try {
                await sock?.logout();
            } catch {}
            sock = null;
            state.connected = false;
            state.qr = null;
            state.phone = null;
            state.pairingCode = null;
            state.status = 'resetting';
            fs.rmSync(AUTH_DIR, { recursive: true, force: true });
            setTimeout(() => startSock(), 500);
            return sendJson(res, 200, { ok: true });
        } catch (err) {
            return sendJson(res, 500, { ok: false, reason: err?.message || 'Gagal mereset sesi.' });
        }
    }

    return sendJson(res, 404, { error: 'Not found' });
});

server.listen(PORT, '127.0.0.1', () => {
    console.log(`[AntreKita WA Gateway] listening on http://127.0.0.1:${PORT}`);
    startSock();
});

const os = require('os');
const fs = require('fs');
const dns = require('dns');
const url = require('url');
const http = require('http');
const querystring = require('querystring');
const PORT = process.env.API_PORT || 3000;
const LOOP_TIMEOUT = 4500;
const data = {ts:0, dt:0};
const listeners = [];
const ajaxHeaders = { 'Access-Control-Allow-Origin': '*', 'Content-Type': 'application/json' };
var ADDR_LAN = '127.0.0.1';

const server = http.createServer(async (req, res) => {
    if (req.method === 'OPTIONS') {
        res.writeHead(204, {
            'Access-Control-Allow-Origin': '*',
            'Access-Control-Allow-Headers': '*',
            'Access-Control-Allow-Methods': 'DELETE, GET, HEAD, OPTIONS, PATCH, POST, PUT',
            'Access-Control-Max-Age': 2592000,
        });
        res.end();
    } else if (req.url === '/api/test' && req.method === 'GET') {
        res.writeHead(200, ajaxHeaders);
        var ts = Date.now();
        res.write(`{"data":"API Resource ${ts}"}`);
        res.end();
    } else if (req.url.match(/^\/api\/log/) && req.method === 'GET') {
        const p = url.parse(req.url);
        const q = p.query ? querystring.parse(p.query) : {};
        res.writeHead(200, ajaxHeaders);
        var ts = Date.now();
        logToFile(`ts=${ts}, msg=${q.msg || ''}`);
        res.write(`{"ts":${ts}}`);
        res.end();
    } else if (req.url.match(/^\/api\/log/) && req.method === 'POST') {
        console.log(req.headers);
        var data = '';
        req.on('data', function (chunk) {
            data += chunk;
            // Too much POST data, kill the connection!
            // 1e6 === 1 * Math.pow(10, 6) === 1 * 1000000 ~~~ 1MB
            if (data.length > 1e6) {
                res.writeHead(413, {'Content-Type': 'text/plain'});
                res.write('Error: out of memory');
                res.end();
                req.connection.destroy();
            }
        });
        req.on('end', function () {
            var ts = Date.now(), body = {};
            try {
                if (req.headers['content-type'] == 'application/x-www-form-urlencoded') {
                    body = querystring.parse(data);
                } else if (req.headers['content-type'] == 'application/json') {
                    body = JSON.parse(data);
                }
                logToFile(`ts: ${ts}; body: ${JSON.stringify(body)}`);
            } catch(e) {
                console.error(e);
                logToFile(`ts: ${ts}; error: ${e.message}`);
            } finally {
                res.writeHead(200, ajaxHeaders);
                res.write(`{"ts":${ts}}`);
                res.end();
            }
        });
    } else if (req.url === '/api/stream' && req.method === 'GET') {
        res.writeHead(200, {
            'Connection': 'keep-alive',
            'Content-Type': 'text/event-stream',
            'Access-Control-Allow-Origin': '*',
        });
        res.on('close', () => {
            listeners.forEach((listener, i) => {
                if (listener === res) {
                    console.log('*clean listener')
                    listeners.splice(i, 1);
                }
            });
            res.end();
        });
        listeners.push(res);
    } else {
        res.writeHead(404, ajaxHeaders);
        res.end(JSON.stringify({ message: 'Route not found' }));
    }
});


dns.lookup(os.hostname(), function (err, addr, fam) {
    ADDR_LAN = addr;
    init();
});

function init() {
    server.listen(PORT, () => {
       const msg = `API server started on: ${ADDR_LAN}:${PORT}`;
       console.log(msg);
       truncateLogFile(msg);
       update();
   });
}

function update() {
    let ts = Date.now();
    data._dt = data.dt;
    data._ts = data.ts;
    data.dt = ts - data.ts;
    data.ts = ts;
    console.log('*update', data.ts);
    updateListeners();
    setTimeout(update, LOOP_TIMEOUT);
}

function updateListeners() {
    listeners.forEach(res => {
        const message = {ts: data.ts, dt: data.dt};
        res.write(`message: serverUpdate\ndata: ${JSON.stringify(message)}\n\n`);
    });
}

function logToFile(msg) {
    fs.writeFileSync('log.txt', msg + '\n\n', {flag:'a'});
}

function truncateLogFile(msg = '') {
    fs.writeFileSync('log.txt', msg ? (msg + '\n\n') : '');
}

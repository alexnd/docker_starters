const http = require('http');
const PORT = process.env.API_PORT || 3000;
const LOOP_TIMEOUT = 4500;
const data = {ts:0, dt:0};
const listeners = [];

const server = http.createServer(async (req, res) => {
    if (req.method === 'OPTIONS') {
        res.writeHead(204, {
            'Access-Control-Allow-Origin': '*',
            'Access-Control-Allow-Methods': 'DELETE, GET, HEAD, OPTIONS, PATCH, POST, PUT',
            'Access-Control-Max-Age': 2592000,
        });
        res.end();
    } else if (req.url === '/api/test' && req.method === 'GET') {
        res.writeHead(200, { 'Access-Control-Allow-Origin': '*', 'Content-Type': 'application/json' });
        var ts = Date.now();
        res.write(`{"data":"API Resource ${ts}"}`);
        res.end();
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
        res.writeHead(404, { 'Access-Control-Allow-Origin': '*', 'Content-Type': 'application/json' });
        res.end(JSON.stringify({ message: 'Route not found' }));
    }
});

server.listen(PORT, () => {
    console.log(`API server started on port: ${PORT}`);
});

update();

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
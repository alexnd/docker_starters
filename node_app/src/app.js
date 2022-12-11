const http = require('http');
const PORT = process.env.API_PORT || 3000;

const server = http.createServer(async (req, res) => {
    if (req.url === '/api/test' && req.method === 'GET') {
        res.writeHead(200, { 'Content-Type': 'application/json' });
        var ts = Date.now();
        res.write(`{"data":"API Resource ${ts}"}`);
        res.end();
    } else {
        res.writeHead(404, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({ message: 'Route not found' }));
    }
});

server.listen(PORT, () => {
    console.log(`API server started on port: ${PORT}`);
});

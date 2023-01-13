// Nodejs server main script

const os = require('os');
const fs = require('fs');
const path = require('path');
const http = require('http');
const https = require('https');
const bodyParser = require('body-parser');
const cookieParser = require('cookie-parser');
const cors = require('cors');
const bcrypt = require('bcrypt');
const jwt = require('jsonwebtoken');
const cron = require('node-cron');
const express = require('express');
const expressHandlebars = require('express-handlebars');
const { I18n } = require('i18n');
const webSocket = require('ws');
const multer = require('multer');
const nodemailer = require('nodemailer');
const DB = require('tingodb')().Db;
const EventEmitter = require('events');
const utils = require('./utils');
const messages = require('./messages');
const package = require('./package.json');

const LOCALES = ['en', 'ua'];
const PORT = process.env.API_PORT || 3000;
const ADDR = process.env.API_ADDR || '0.0.0.0';
const ROOT_PATH = process.env.ROOT_PATH || '/api';
const DB_COLLECTIONS = ['users', 'tasks'];
const DB_PATH = process.env.DB_PATH || path.resolve(__dirname, 'data');
const sseListeners = [];
const corsHeaders = {
  'Access-Control-Allow-Origin': '*',
  'Access-Control-Allow-Methods': 'DELETE, GET, HEAD, OPTIONS, PATCH, POST, PUT',
  // 'Access-Control-Allow-Methods-Headers': 'Accept, Host, Connection, Content-Type, Cookie, x-requested-with',
  'Access-Control-Max-Age': 2592000,
};
const store = {
  rates: {
    USD: 0,
    USDT: 0,
  },
};
//console.log('*[env]', process.env);

// init embedded DB
if (!fs.existsSync(DB_PATH)) {
  fs.mkdirSync(DB_PATH);
}
if (!fs.existsSync(DB_PATH)) {
  console.log('Error: can not init db');
  process.exit(1);
}
for (const col of DB_COLLECTIONS) {
  let colPath = path.resolve(DB_PATH, col);
  if (!fs.existsSync(colPath)) {
    fs.writeFileSync(colPath, '');
  }
  if (!fs.existsSync(colPath)) {
    console.log(`Error: can not init ${col} collection`);
    process.exit(1);
  }
}
const db = new DB(DB_PATH, {});

const i18n = new I18n({
  locales: LOCALES,
  directory: path.join(__dirname, 'locales'),
  defaultLocale: process.env.DEFAULT_LOCALE || 'en',
});

// init Express
const app = express();

//https://www.npmjs.com/package/express-handlebars
const handlebars = expressHandlebars.create({
extname: '.hbs',
views: './views',
helpers: {
  json: v => JSON.stringify(v),
  activeClass: (value, expectedValue) => { return value === expectedValue ? ' class="active"' : '' },
},
});
app.engine('.hbs', handlebars.engine);
app.set('view engine', '.hbs');
app.set('views', './views');

// express middlewares
app.use(i18n.init);
app.use(cookieParser());
app.use(bodyParser.urlencoded({ extended: false }));
app.use(bodyParser.json());
app.use(cors());

// main middleware
app.use((req, res, next) => {
  // footprint log
  console.log(utils.dt(), req.connection.remoteAddress, req.method, req.url);
  // isAjax
  const ctype = `${req.header('content-type') || req.header('accept') || ''}`;
  const xrw = `${req.header('x-requested-with') || ''}`;
  req.isAjax = ctype && ctype.match('json') || xrw && xrw.match(/XMLHttpRequest/i) || req.xhr;
  // current locale (if not set, detected by i18n automatically from Accept-Language header)
  const userLocale = req.cookies && req.cookies.lang ? req.cookies.lang : null;
  if (userLocale && LOCALES.indexOf(userLocale) !== -1) {
    req.setLocale(userLocale);
  }
  // sendError
  res.sendError = (error, code = 200) => {
    if (req.isAjax) {
      res.status(code).json({error});
    } else {
      res.status(code).send(`<h1>Error: ${error}</h1>`);
    }
  };
  // allow cors
  // if (req.method === 'OPTIONS') {
  //  res.writeHead(204, corsHeaders);
  //  res.end();
  // } else
  // SSE connect
  if (req.url === (ROOT_PATH ? `/${ROOT_PATH}/stream` : '/stream') && req.method === 'GET') {
    res.status(200).set({...corsHeaders, ...{
      'Connection': 'keep-alive',
      'Content-Type': 'text/event-stream',
      'Cache-Control': 'no-cache',
      'X-Accel-Buffering': 'no',
    }});
    res.flushHeaders();
    res.write('retry: 5000\n\n');
    res.on('close', () => {
      sseListenerRemove(res);
      res.end();
    });
    sseListenerAdd(res);
  } else {
    next();
  }
});

// init HTTP server
const httpServer = http.createServer(app);

// Websockets
const ws = new webSocket.Server({ server: httpServer });
ws.on('connection', (socket, req) => {
  // https://www.npmjs.com/package/ws#client-authentication
  const ip = req.socket.remoteAddress || req.headers['x-forwarded-for'].split(',')[0].trim();;
  console.log(`*[WS] ${ip} connected`);
  socket.on('message', message => {
    console.log('*[WS] message', typeof message, message.toString('utf-8'));
    // wsBroadcast(message.toJSON());
  });
});

// app shared context
const context = {
  PORT,
  ROOT_PATH,
  LOCALES,
  NAME: process.env.APP_NAME || 'AppName',
  appPath: __dirname,
  package,
  store,
  models: {},
  utils,
  messages,
  os,
  fs,
  db,
  jwt,
  app,
  cron,
  path,
  http,
  https,
  httpServer,
  bcrypt,
  express,
  handlebars,
  expressHandlebars,
  webSocket,
  ws,
  EventEmitter,
  nodemailer,
  i18n,
  upload: multer(),
  corsHeaders,
  sseListeners,
  sseBroadcast,
  wsBroadcast,
};

// base DB model class
context.baseModel = require('./base_model')(context);

// autoload app services
fs.readdirSync(path.resolve(__dirname, 'services')).forEach(file => {
  const service = file.split('.')[0];
  if (!service || file && !file.match('\.js$')) return;
  context['_' + service] = require('./services/' + file)(context);
});

// autoload app models
fs.readdirSync(path.resolve(__dirname, 'models')).forEach(file => {
  const model = file.split('.')[0];
  if (!model || file && !file.match('\.js$')) return;
  context.models[model] = require('./models/' + file)(context);
});

// autoload html routes
fs.readdirSync(path.resolve(__dirname, 'routes', 'web')).forEach(file => {
  const route = file.split('.')[0];
  if (!route || file && !file.match('\.js$')) return;
  console.log('*[web routes]', './routes/web/' + file);
  const r = require('./routes/web/' + file)(context);
  app.use(route === 'index' ? '/' : `/${route}`, r);
});
// autoload /api routes
fs.readdirSync(path.resolve(__dirname, 'routes', 'api')).forEach(file => {
  const route = file.split('.')[0];
  if (!route || file && !file.match('\.js$')) return;
  console.log('*[api routes]', './routes/api/' + file);
  const r = require('./routes/api/' + file)(context);
  app.use(route === 'index' ? (ROOT_PATH ? ROOT_PATH : '/') : `${ROOT_PATH}/${route}`, r);
});

// host frontend static
if (process.env.STATIC_PATH) {
  app.use(express.static(process.env.STATIC_PATH));
}

// 404 route
app.all('*', (req, res) => {
  res.sendError(messages.notFound, 404);
});

// errors handling
app.use((err, req, res, next) => {
  console.error(err.stack);
  res.sendError(err.message || messages.systemError, 500);
});

// start server
httpServer.listen(PORT, ADDR, () => {
  console.log(`API server started on http://localhost:${PORT}`);
});

// run cron jobs queue
fs.readdirSync(path.resolve(__dirname, 'jobs')).forEach(file => {
  const id = file.split('.')[0];
  if (!id || file && !file.match('\.js$')) return;
  const jobs = require('./jobs/' + file)(context);
  if (!Array.isArray(jobs)) return;
  for (const [time, cb] of jobs) {
    console.log(`*[new cron job] ${file} ${time}`);
    cron.schedule(time, cb);
  }
});

// broadcast to websockets
function wsBroadcast(message) {
  ws.clients.forEach(client => {
    if (client.readyState === webSocket.OPEN) {
      if (typeof message === 'object') {
        client.send(JSON.stringify(message));
      } else {
        client.send(message);
      }
    }
  });
}

// broadcast message to SSE listeners
function sseBroadcast(payload, id = 'serverUpdate') {
  for (const res of sseListeners) {
    res.write(`event: ${id}\ndata: ${JSON.stringify(payload)}\n\n`);
  }
}

// SSE listener add
function sseListenerAdd(res) {
  console.log('*[open SSE listener]');
  sseListeners.push(res);
}

// SSE listener remove
function sseListenerRemove(res) {
  for (let i = 0; i < sseListeners.length; i++) {
    if (sseListeners[i] !== res) continue;
    console.log('*[SSE listener closed]');
    sseListeners.splice(i, 1);
    break;
  }
}

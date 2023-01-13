// sockets pings

module.exports = app => {

  const data = {ts:0, dt:0};

  return [
    [
      '*/15 * * * * *',
      () => {
        const ts = Date.now();
        data.dt = ts - data.ts;
        data.ts = ts;
        console.log('*[chat ping]', app.helpers.dt(data.ts));
        const message = {...app.store, ...{ts: data.ts, dt: data.dt}};
        app.sseBroadcast(message);
        app.wsBroadcast({ from_user: 'root', to_user: '', text: `Now is ${app.helpers.dt()}`, created_at: ts }, 'chat message');
      }
    ],
  ];

}

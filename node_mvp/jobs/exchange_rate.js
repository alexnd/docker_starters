// job to fetch currencies rates from rest api

module.exports = app => {

  const fetchExchanges = cb => {
    if (!process.env.CRYPTOCOMPARE_API_KEY) {
      return cb(new Error('CRYPTOCOMPARE_API_KEY not set'))
    }
    const url = `https://min-api.cryptocompare.com/data/price?fsym=ETH&tsyms=USD,USDC,USDT&api_key=${process.env.CRYPTOCOMPARE_API_KEY}`;
    //console.log('*fetch:', url);
    app.https.
      get(url, res => {
        let data = '';
        res.on('data', chunk => {
          data += chunk;
        });
        res.on('end', () => {
          let json = {};
          if (data) {
            try {
              Object.assign(json, JSON.parse(data));
            } catch (e) {
              console.error(e);
            }
          }
          console.log('*json', json);
          cb && cb(null, json);
        });
      })
      .on('error', err => {
        console.error(err);
        cb && cb(err);
      });
  }

  return [[
    '5 */21 * * * *',
    () => {
      fetchExchanges((err, data) => {
        if (err) return;
        console.log('*[fetchExchanges]', data);
        if (typeof data === 'object' && data !== null) {
          app.store.rates = data;
        } else {
          console.log('*[fetchExchanges error]', app.helpers.dt(), 'invalid data object');
        }
      });
    }],
  ];

}

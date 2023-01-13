// web (html) routes
module.exports = app => {
  const router = app.express.Router();
  const logoutBody = '<html><head><meta http-equiv="refresh" content="2;url=/login"></head><body><script>localStorage.removeItem("auth-token")</script></body></html>';

  const computeRouteVars = (req, route = '') => {
    const isRouteActive = route ? req.path.indexOf(route) === 1 : req.path === '/';
    const lang = req.getLocale();
    const vars = {
      path: req.path,
      lang,
      activeRoute: (isRouteActive ? (route ? route : 'index') : ''),
      isLoginRoute: req.path === '/login',
      isProfileRoute: req.path === '/profile',
      isLangUa: lang === 'ua',
      isLangEn: lang === 'en',
      rates: app.store.rates,
    }
    return vars;
  }

  router.get('/', (req, res) => {
    res.render('index', computeRouteVars(req));
  });

  router.get('/contacts', (req, res) => {
    res.render('contacts', computeRouteVars(req, 'contacts'));
  });

  router.get('/login', (req, res) => {
    res.render('login', computeRouteVars(req, 'login'));
  });

  router.get('/logout', (req, res) => {
    res.send(logoutBody);
  });

  router.get('/register', (req, res) => {
    res.render('register', computeRouteVars(req, 'register'));
  });

  router.get('/reset', (req, res) => {
    res.render('reset_password_request', computeRouteVars(req, 'reset'));
  });

  router.get('/reset-confirm', (req, res) => {
    res.render('reset_password_confirm', {...{token: req.query.token, ...computeRouteVars(req, 'reset-confirm')}});
  });

  router.get('/profile', (req, res) => {
    res.render('profile', computeRouteVars(req, 'profile'));
  });

  router.get('/lang/:lang', (req, res) => {
    if (app.LOCALES.indexOf(req.params.lang) === -1) {
      res.redirect('back');
    } else {
      res.cookie('lang', req.params.lang).redirect('back');
    }
  });

  return router;
}

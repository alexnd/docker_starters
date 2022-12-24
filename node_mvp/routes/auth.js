// authorization and user stuff
module.exports = app => {
  const router = app.express.Router();
  const dbCollecionName = 'users';

  // test token still valid
  router.get('/', app._auth.testAuth, (req, res) => {
    res.status(200).json({ success: !!(req.user && req.user.id) });
  });

  // sign in with GET method and return jwt
  router.get('/login', (req, res) => {
    if (req.query.username && req.query.password) {
      app._auth
        .authenticate(req.query)
        .then(user => {
          app._auth
            .createAccessToken({ id: user._id })
            .then(token => {
              res.status(200).json({
                success: true,
                token,
                email: user.email || '',
                username: req.body.username,
                name: req.body.username,
              });
            })
            .catch(err => res.sendError(err.message || app.messages.accessDenied, 401));
        })
        .catch(err => res.sendError(err.message || app.messages.accessDenied, 401));
    } else {
      res.sendError(app.messages.credentialsRequired, 422);
    }
  });

  // sign in with POST method and return jwt
  router.post('/login', (req, res) => {
    if (req.body.username && req.body.password) {
      app._auth
        .authenticate(req.body)
        .then(user => {
          app._auth
            .createAccessToken({ id: user._id })
            .then(token => {
              res.status(200).json({
                success: true,
                token,
                email: user.email || '',
                username: req.body.username,
                name: req.body.username,
              });
            })
            .catch(err => res.sendError(err.message || app.messages.systemError, 500));
        })
        .catch(err => res.sendError(err.message || app.messages.accessDenied, 401));
    } else {
      res.sendError(app.messages.credentialsRequired, 422);
    }
  });

  // sign up new user
  router.post('/register', (req, res) => {
    let userFields = {
      ...(req.body.username && {username: req.body.username}),
      ...(req.body.email && {email: req.body.email}),
      ...(req.body.name && {name: req.body.name}),
      ...(req.body.password && {password: req.body.password}),
    };
    if (userFields.username && userFields.password) {
      if (userFields.name === undefined) userFields.name = '';
      if (userFields.email === undefined) userFields.email = '';
      app.models.user
        .get({ username: userFields.username })
        .then(user => {
          console.log('*user', user);
          if (user) {
            res.sendError(app.messages.registerUserExist, 422);
          } else {
            app._auth
              .hashPassword(userFields.password)
              .then(passwordHash => {
                let data = {
                  password: passwordHash,
                  restore_token: app.utils.uid(app.utils.rnd(18, 23)), 
                  created_at: Date.now(),
                  updated_at: Date.now(),
                };
                app.models.user
                  .create({...userFields, ...data})
                  .then(result => {
                    console.log('*[user create]', typeof result, result);
                    if (result) {
                      app._auth
                        .createAccessToken({ id: result._id})
                        .then(token => {
                          res.status(201).json({
                            success: true,
                            token,
                            username: userFields.username,
                            email: userFields.email,
                            name: userFields.name,
                            restore_token: data.restore_token,
                          });
                        })
                        .catch(err => {
                          res.sendError(err.message, 500);
                        });
                    } else {
                      res.sendError(app.messages.systemError, 500);
                    }
                  })
                  .catch(err => res.sendError(err.message || '', 500));
              })
              .catch(err => res.sendError(err.message, 500));
          }
        })
        .catch(err => res.sendError(err.message || '', 500));
    } else {
      res.sendError(app.messages.userFieldsRequired, 422);
    }
  });

  // refresh token
  // app._auth.checkAuth, 
  router.get('/refresh', (req, res) => {
    // TODO: refresh token gets from cookie and validates with another refresh secret key
    // req.cookies.refreshToken
    res.json({ success: false });
  })

  // blacklist token
  router.get('/logout', app._auth.checkAuth, (req, res) => {
    // TODO
    res.json({ success: true });
  });

  // return current authorized user's profile
  router.get('/user', app._auth.checkAuth, (req, res) => {
    app.models.user
      .get({ _id: req.user.id })
      .then(user => {
        console.log('*user', user);
        if (user) {
          delete user._id;
          delete user.password;
          delete user.restore_token;
          res.json(user);
        } else {
          res.sendError(app.messages.notFound, 404);
        }
      })
      .catch(err => res.sendError(err.message, 500));
  });

  // update user's profile 
  router.post('/user', app._auth.checkAuth, (req, res) => {
    let userFields = {
      ...(req.body.username && {username: req.body.username}),
      ...(req.body.email && {email: req.body.email}),
      ...(req.body.name && {name: req.body.name}),
    };
    console.log('*userFields', userFields);
    if (!Object.keys(userFields).length) {
      res.sendError(app.messages.userFieldsRequired, 422);
      return;
    }
    userFields.updated_at = Date.now();
    const userUpdate = dto => {
      console.log('*userDto', dto);
      app.models.user
        .update({ _id: req.user.id }, dto)
        .then(model => {
          console.log('*model', model);
          if (model) {
            res.status(200).json({ success: true });
          } else {
            res.status(200).json({ success: false });
          }
        })
        .catch(err => res.sendError(err.message || app.messages.systemError, 500));
    }
    app.models.user
      .get({_id: req.user.id})
      .then(user => {
        if (user) {
          delete user._id;
          delete user.password;
          userFields.updated_at = Date.now();
          userFields.restore_token = app.utils.uid(app.utils.rnd(18, 23));
          if (req.body.password) {
            app._auth
              .hashPassword(req.body.password)
              .then(passwordHash => {
                userFields.password = passwordHash;
                userUpdate({...user, ...userFields});
              })
              .catch(err => res.sendError(err.message, 500));
          } else {
            userUpdate({...user, ...userFields});
          }
        } else {
          res.sendError(app.messages.notFound, 404);
        }
      })
      .catch(err => res.sendError(err.message || app.messages.systemError, 500));
  });

  // return user's profile
  router.get('/user/:username', app._auth.checkAuth, (req, res) => {
    if (req.params.username) {
      app.models.user
        .get({ username: req.params.username })
        .then(user => {
          if (user) {
            console.log('*user', user);
            delete user._id;
            delete user.password;
            delete user.restore_token;
            res.status(200).json(user);
          } else {
            res.sendError(app.messages.notFound, 404);
          }
        })
        .catch(err => res.sendError(err.message || app.messages.systemError, 500));
    } else {
      res.sendError(app.messages.usernameRequired, 422);
    }
  });

  router.get('/reset', app._auth.testAuth, (req, res) => {
    if (req.query.token) {
      app.models.user
        .get({ restore_token: req.query.token })
        .then(user => {
          if (user) {
            // TODO: send reset login email to user.email
            res.json(user);
          } else {
            res.sendError(app.messages.notFound, 404);
          }
        })
        .catch(err => res.sendError(err.message || app.messages.systemError, 500));
    } else {
      res.sendError(app.messages.userFieldsRequired, 422);
    }
  });

  return router;
}
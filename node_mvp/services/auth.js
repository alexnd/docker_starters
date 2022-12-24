// authorization

module.exports = app => {
  const dbCollecionName = 'users';

  return {

    // auth check middleware. blocks request if not pass
    checkAuth: (req, res, next) => {
      // make authenticate path public
      if (req.path === '/login' || req.path === '/auth/login') {
        return next();
      }
      const reject = () => {
        res.sendError(app.messages.accessDenied, 401);
      }
      // check for auth header
      const token = (
        req.headers.authorization &&
        req.headers.authorization.indexOf('Bearer ') === 0
      ) ? req.headers.authorization.split(' ')[1] : '';
      if (!token) {
        return reject();
      }
      app.jwt.verify(token, process.env.AUTH_SECRET, (err, data) => {
        if (err) {
          console.log('*jwt.verify error', err);
          return reject();
        }
        req.user = data;
        next();
      });
    },

    // auth test middleware. not blocks request if not pass. adds req.user if success
    testAuth: (req, res, next) => {
      const token = (
        req.headers.authorization &&
        req.headers.authorization.indexOf('Bearer ') === 0
      ) ? req.headers.authorization.split(' ')[1] : '';
      if (token) {
        app.jwt.verify(token, process.env.AUTH_SECRET, (err, data) => {
          if (err) {
            console.log('*jwt.verify error', err);  
          } else {
            req.user = data;
          }
          next();
        });
      } else {
        next();
      }
    },
    
    authenticate(credentials) {
      return new Promise((resolve, reject) => {
        if (credentials.username && credentials.password) {
          app.models.user
            .get({ username: credentials.username })
            .then(model => {
              if (model) {
                app.bcrypt
                  .compare(credentials.password, model.password)
                  .then(success => {
                    if (success) {
                      resolve(model);
                    } else {
                      reject({ message: app.messages.accessDenied });
                    }
                  })
                  .catch(err => reject(err));
              } else {
                reject({ message: app.messages.notFound });
              }
            })
            .catch(err => reject(err));
        } else {
          reject({ message: app.messages.credentialsRequired });
        }
      });
    },

    createAccessToken(payload) {
      return new Promise((resolve, reject) => {
        if (process.env.AUTH_SECRET) {
          app.jwt.sign(
            payload,
            process.env.AUTH_SECRET,
            { expiresIn: '1d' },
            (err, token) => {
              if (err) {
                reject({ message: err.message });
              } else {
                resolve(token);
              }
            });
        } else {
          reject({ message: 'AUTH_SECRET not set' });
        }
      })
    },

    hashPassword(password) {
      return new Promise((resolve, reject) => {
        app.bcrypt
          .hash(password, 10)
          .then(hash => resolve(hash))
          .catch(err => reject({ message: err.message }));
      })
    }
  }
}
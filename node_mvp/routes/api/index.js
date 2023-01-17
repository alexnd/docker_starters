module.exports = app => {
    const router = app.express.Router();
    const pathCountriesJson = app.path.join(app.appPath, 'data', 'countries.json');
  
    router.get('/', (req, res) => {
      if (req.isAjax) {
        res.json({ version: app.package.version });
      } else {
        res.send(`version: ${app.package.version}`);
      }
    });

    router.get('/test/:id?', (req, res) => {
      if (req.params.id) {
        app.models.task
          .get(req.params.id)
          .then(task => {
            if (task) {
              res.json(task);
            } else {
              res.sendError(app.i18n.__('notFound'), 404);
            }
          })
          .catch(err => res.sendError(err.message || app.i18n.__('systemError'), 500));
      } else {
        app.models.task.all()
          .then(tasks => res.json(tasks))
          .catch(err => res.sendError(err.message || app.i18n.__('systemError'), 500));
      }
    });

    router.post('/test/:id?', (req, res) => {
      const ts = Date.now();
      const user_id = 3;
      const dto = {
        user_id,
        name: req.body.name || `Task ${ts}`,
        done: false,
        group_id: 1,
        sort_order: 0,
        created_at: ts,
        updated_at: ts,
      };
      if (req.params.id) {
        const id = +req.params.id;
        app.models.task
          .update({_id: id}, dto)
          .then((err, result) => {
            console.log('*[update result]', result);
            if (result) {
              res.status(200).json({success: true, id});
            } else {
              res.status(200).json({success: false});
            }
          })
          .catch(err => res.sendError(err.message || app.i18n.__('systemError'), 500));
      } else {
        app.models.task
          .create(dto)
          .then(result => {
            console.log('*[create result]', result);
            if (result) {
              res.status(201).json({success: true, id: result._id});
            } else {
              res.status(200).json({success: false});
            }
          })
          .catch(e => res.sendError(e.message || app.i18n.__('systemError'), 500));
      }
    });

    router.get('/countries', (req, res) => {
      if (app.fs.existsSync(pathCountriesJson))
        res.sendFile(pathCountriesJson);
      else
        res.sendError(app.i18n.__('notFound'), 404);
    });

    router.get('/users', (req, res) => {
      app.models.user.all()
        .then(result => res.status(200).json(result))
        .catch(e => res.sendError(e.message || app.i18n.__('systemError'), 500));
    });

    router.get('/initdata', app._auth.testAuth, (req, res) => {
      const countries = app.fs.existsSync(pathCountriesJson)
        ? JSON.parse(app.fs.readFileSync(pathCountriesJson))
        : [];
      res.json({
        auth: req.user && req.user.id,
        data: {
          ts: Date.now(),
          countries,
        },
      });
    })

    return router;
  }
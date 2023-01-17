module.exports = app => {
  const router = app.express.Router();
  const dbCollecionName = 'tasks';

  // list tasks
  router.get('/', app._auth.checkAuth, (req, res) => {
    app.models.task
      .getAll({user_id: req.user.id})
        .then(tasks => {
          if (tasks && tasks.length) {
            res.status(200).json(tasks.map(item => { return {
              id: item._id,
              name: item.name,
              done: item.done,
              groupId: item.group_id,
              sortOrder: item.sort_order,
              created: item.created_at,
            }}));
          } else {
            res.status(200).json([]);
          }
      })
      .catch(err => res.sendError(err.message || app.i18n.__('systemError'), 500));
  });

  // create|update task
  router.post('/', app._auth.checkAuth, (req, res) => {
    // console.log('*task post body', req.body);
    if (req.body.name) {
      const ts = Date.now();
      if (req.body.id) {
        const data = {
          user_id: req.user.id,
          ...(req.body.name && {name: req.body.name}),
          ...(req.body.done !== undefined && {done: !!req.body.done}),
          ...(req.body.groupId !== undefined && {group_id: req.body.groupId || 0}),
          ...(req.body.sortOrder !== undefined && {sort_order: req.body.sortOrder || 0}),
          updated_at: ts,
        };
        app.models.task
          .update({user_id: req.user.id, _id: req.body.id}, data)
          .then(result => {
            if (result) {
              res.status(200).json({success: true, id: req.body.id});
            } else {
              res.status(200).json({success: false});
            }
          })
          .catch(err => res.sendError(err.message || app.i18n.__('systemError'), 500));
      } else {
        const data = {
          user_id: req.user.id,
          name: req.body.name || '',
          done: req.body.done || false,
          group_id: req.body.groupId || 0,
          sort_order: req.body.sortOrder || 0,
          created_at: ts,
          updated_at: ts,
        };
        app.models.task
          .create(data)
          .then(result => {
            if (result && result._id) {
              res.status(201).json({success: true, id: result._id});
            } else {
              res.status(200).json({success: false});
            }
          })
          .catch(err => res.sendError(err.message || app.i18n.__('systemError'), 500));
      }
    } else {
      res.sendError(app.i18n.__('fieldsRequired'), 422);
    }
  });
  
  // update task
  router.put('/:id', app._auth.checkAuth, (req, res) => {
    if (req.body && Object.keys(req.body).length) {
      const data = {
        user_id: req.user.id,
        ...(req.body.name && {name: req.body.name}),
        ...(req.body.done !== undefined && {done: !!req.body.done}),
        ...(req.body.groupId !== undefined && {group_id: req.body.groupId || 0}),
        ...(req.body.sortOrder !== undefined && {sort_order: req.body.sortOrder || 0}),
        updated_at: Date.now(),
      };
      app.models.task
        .update({user_id: req.user.id, _id: req.params.id}, data)
        .then(result => {
          if (result) {
            res.status(200).json({success: true, id: req.params.id});
          } else {
            res.status(200).json({success: false});
          }
        })
        .catch(err => res.sendError(err.message || app.i18n.__('systemError'), 500));
    } else {
      res.sendError(app.i18n.__('fieldsRequired'), 422);
    }
  });

  // update task status (done) only
  router.post('/done', app._auth.checkAuth, (req, res) => {
    if (req.body && req.body.id) {
      app.models.task
        .update({user_id: req.user.id, _id: req.body.id}, {done: req.body.done || false})
        .then(result => res.status(200).json({success: !!result}))
        .catch(err => res.sendError(err.message || app.i18n.__('systemError'), 500));
    } else {
      res.sendError(app.i18n.__('fieldsRequired'), 422);
    }
  });
  router.post('/done/:id', app._auth.checkAuth, (req, res) => {
    if (req.body && req.params.id) {
      app.models.task
        .update({user_id: req.user.id, _id: req.params.id}, {done: req.body.done || false})
        .then(result => res.status(200).json({success: !!result}))
        .catch(err => res.sendError(err.message || app.i18n.__('systemError'), 500));
    } else {
      res.sendError(app.i18n.__('fieldsRequired'), 422);
    }
  });

  // delete task
  router.delete('/:id', app._auth.checkAuth, (req, res) => {
    if (req.params.id) {
      app.models.task
        .delete({user_id: req.user.id, _id: req.params.id})
        .then(result => res.status(200).json({success: !!result}))
        .catch(err => res.sendError(err.message || app.i18n.__('systemError'), 500));
    } else {
      res.sendError(app.i18n.__('fieldsRequired'), 422);
    }
  });

  // delete tasks by ids
  router.delete('/', app._auth.checkAuth, (req, res) => {
    if (req.body.id) {
      app.models.task
        .delete({user_id: req.user.id, _id: req.body.id})
        .then(result => res.status(200).json({success: !!result}))
        .catch(err => res.sendError(err.message || app.i18n.__('systemError'), 500));
    } else if (req.body.ids) {
      app.models.task
        .delete({user_id: req.user.id, _id: { $in: req.body.ids }})
        .then(result => {
          console.log('*task delete by ids result', result);
          res.status(200).json({success: !!result});
        })
        .catch(err => res.sendError(err.message || app.i18n.__('systemError'), 500));
    } else {
      res.sendError(app.i18n.__('fieldsRequired'), 422);
    }
  });

  return router
}

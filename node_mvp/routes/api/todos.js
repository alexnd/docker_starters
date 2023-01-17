module.exports = app => {
  const router = app.express.Router();
  const pathTodosJson = app.path.join(app.appPath, 'data', 'todos.json');


  router.get(/^\/todos|TODO$/, (req, res) => {
    res.sendFile(pathTodosJson);
  });

  router.post(/^\/todos|TODO$/, (req, res) => {
    if (req.body && Object.keys(req.body).length) {
      let id = +req.body.id || 0;
      let collection = JSON.parse(app.fs.readFileSync(pathTodosJson));
      const i = id ? collection.findIndex(item => item.id === id) : -1;
      if (i === -1) {
          //collection.push({...{id: app.utils.uid(16)}, ...(req.body || {})});
          id = Math.max(...collection.map(o => o.id)) + 1;
          collection.push({
            ...(req.body || {}),
            ...{id},
          });
      } else {
          collection[i] = {...collection[i], ...(req.body || {})};
      }
      try {
        app.fs.writeFileSync(pathTodosJson, JSON.stringify(collection));
        res.status(i === -1 ? 201 : 200).json({success: true, id});
      } catch (err) {
        res.sendError(err.message || app.i18n.__('operationFailed'), 500);
      }
    } else {
      res.sendError(app.i18n.__('fieldsRequired'), 422);
    }
  });

  router.delete(/^\/todos|TODO$/, (req, res) => {
    const id = req.body.id || 0;
    if (id) {
      let collection = JSON.parse(app.fs.readFileSync(pathTodosJson));
      const i = collection.findIndex(item => item.id === id);
      if (i !== -1) {
        collection.splice(i, 1);
        try {
          app.fs.writeFileSync(pathTodosJson, JSON.stringify(collection));
          res.status(200).json({success: true});
        } catch (err) {
          res.sendError(err.message || app.i18n.__('operationFailed'), 500);
        }
      } else {
        res.sendError(app.i18n.__('notFound'), 404);
      }
    } else {
      res.sendError(app.i18n.__('fieldsRequired'), 422);
    }
  });

  return router;
}
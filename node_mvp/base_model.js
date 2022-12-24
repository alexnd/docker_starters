// Base TingoDB model with CRUD operations

module.exports = app => {

  class BaseModel {

    constructor(table, proto) {
      this.table = table
      if (typeof proto === 'object') {
        Object.assign(this, proto);
      }
    }

    all() {
      return new Promise((resolve, reject) => {
        app.db.collection(this.table).find({}).toArray((err, res) => {
          if (err) {
            reject(err);
          } else {
            resolve(res);
          }
        });
      });
    }

    get(o) {
      var where = typeof o === 'object' ? o : {_id: o};
      return new Promise((resolve, reject) => {
        app.db.collection(this.table).findOne(where, (err, res) => {
          if (err) {
            reject(err);
          } else {
            resolve(res);
          }
        });
      });
    }

    getAll(o) {
      return new Promise((resolve, reject) => {
        app.db.collection(this.table).find(o || {}).toArray((err, res) => {
          if (err) {
            reject(err);
          } else {
            resolve(res);
          }
        });
      });
    }

    create(o) {
      return new Promise((resolve, reject) => {
        if (Array.isArray(o) && o.length) {
          app.db.collection(this.table).insert(o, {w: 1}, (err, result) => {
            if (err) {
              reject(err);
            } else {
              resolve(result);
            }
          });
        } else if (typeof o === 'object' && o !== null) {
          app.db.collection(this.table).insert([o], {w: 1}, (err, result) => {
            if (err) {
              reject(err);
            } else {
              if (result && Array.isArray(result) && result.length) {
                resolve(result[0]);
              } else {
                resolve(null);
              }
            }
          });
        } else {
          reject(new Error(app.messages.invalidInput));
        }
      });
    }

    replace(o, p) {
      return new Promise((resolve, reject) => {
        app.db.collection(this.table).update(
          o,
          p,
          {},
          (err, res) => {
            if (err) {
              reject(err);
            } else {
              resolve(res);
            }
          }
        );
      });
    }

    update(o, p) {
      return new Promise((resolve, reject) => {
        //col.update(
        //  o,
        //  { $set: p },
        //  { upsert: true },
        //  (err, model) => { }
        //);
        app.db.collection(this.table).findOne(o || {}, (err, obj) => {
          if (err) {
            reject(err);
          } else {
            if (obj) {
              app.db.collection(this.table).findAndModify(
                o,
                null,
                {...obj, ...p},
                {},
                (err, res) => {
                  if (err) {
                    reject(err);
                  } else {
                    resolve(res);
                  }
                }
              );
            } else {
              resolve(null);
            }
          }
        });
      });
    }

    updateOrCreate(o, p) {
      return new Promise((resolve, reject) => {
        app.db.collection(this.table).findOne(o || {}, (err, obj) => {
          if (err) {
            reject(err);
          } else {
            if (obj) {
              app.db.collection(this.table).findAndModify(
                o,
                null,
                {...obj, ...p},
                {},
                (err, res) => {
                  if (err) {
                    reject(err);
                  } else {
                    resolve(res);
                  }
                }
              );
            } else {
              app.db.collection(this.table).insert([p], {w: 1}, (err, res) => {
                if (err) {
                  reject(err);
                } else {
                  resolve(res);
                }
              });
            }
          }
        });
      });
    }

    delete(o) {
      return new Promise((resolve, reject) => {
        if (Array.isArray(o)) {
          app.db.collection(this.table).remove(
            {
              user_id: req.user.id,
              _id: { $in: req.body.ids },
            },
            {},
            (err, res) => {
              if (err) {
                reject(err);
              } else {
                resolve(res);
              }
            }
          );
        } else {
          app.db.collection(this.table).remove(
            o,
            {},
            (err, res) => {
              if (err) {
                reject(err);
              } else {
                resolve(res);
              }
            }
          );
        }
      });
    }

  }

  return BaseModel;
}
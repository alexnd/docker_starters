const utils = {

    rnd(min, max) {
      return Math.floor(Math.random() * (max - min + 1)) + min;
    },
  
    dt(d) {
      return (d ? new Date(d) : new Date()).toISOString().substr(0, 19);
    },
  
    uuid() {
      let s = () => {
        return (((1 + Math.random()) * 0x10000) | 0).toString(16).substring(1);
      };
      return `${s()}${s()}-${s()}-${s()}-${s()}-${s()}${s()}${s()}`;
    },
  
    uid(len) {
      let buf = [],
        chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',
        n = len || 32;
      for (var i = 0; i < n; ++i) {
        buf.push(chars[utils.rnd(0, 61)]);
      }
      return buf.join('');
    },
  
    uniqueArray(a) {
      return a.filter((item, pos, self) => {
        return self.indexOf(item) == pos;
      });
    },
  
  };
  
  module.exports = utils;
  
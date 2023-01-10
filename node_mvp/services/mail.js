// wrapper for nodemailer

module.exports = app => {
  var _transport = null;
  getTransport = () => {
    if (_transport) return _transport;
    if (!process.env.SENDMAIL_HOST) return null;
    _transport = app.nodemailer.createTransport({
      host: process.env.SENDMAIL_HOST,
      port: process.env.SENDMAIL_PORT || 25,
      secure: false,
      tls: {
        rejectUnauthorized: false
      }
    });
    return _transport ?? null;
  }
  return {
    getTransport,
    sendMail({to, subject, body, from = ''}) {
      if (!getTransport()) return Promise.resolve(true);
      return getTransport().sendMail({
        to,
        subject,
        from: from || process.env.SENDMAIL_FROM || `no-reply@${app.os.hostname()}`,
        html: body,
      });
    }
  }
}
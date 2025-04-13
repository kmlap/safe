const pako = require('pako');

module.exports = function (message) {
  if (process.env.GZIP==='on') {
    if (typeof (message) === 'string') {
      return pako.gzip(message);
    }
    if (typeof message === 'object') {
      return pako.gzip(JSON.stringify(message));
    }
  } else {
    if (typeof (message) === 'string') {
      return message;
    }
    if (typeof message === 'object') {
      return JSON.stringify(message);
    }
  }
}

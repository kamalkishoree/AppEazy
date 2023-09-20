var express = require('express');
var createError = require('http-errors');
var path = require('path');
var favicon = require('serve-favicon');
var logger = require('morgan');
var bodyParser = require('body-parser');

var room = require('./routes/roomRoute');
var chat = require('./routes/chatRoute');
var UserLocation = require('./routes/userRoute');

var app = express();

var mongoose = require('mongoose');
mongoose.Promise = require('bluebird');
mongoose.connect('mongodb://devchatuser:kjenbci43f0943ujfoi4309fu43j@localhost:27017/mevn-chat', { useNewUrlParser: true, promiseLibrary: require('bluebird') })
  .then(() =>  console.log('connection succesful'))
  .catch((err) => console.error(err));

app.use(logger('dev'));
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({'extended':'false'}));
app.use(express.static(path.join(__dirname, 'dist')));
app.use('/rooms', express.static(path.join(__dirname, 'dist')));
app.use('/api/room', room);
app.use('/api/chat', chat);
app.use('/api/agent', UserLocation);


// catch 404 and forward to error handler
app.use(function(req, res, next) {
  next(createError(404));
});

// var server = require('http').createServer(app);
// global.ioSocket = require('socket.io')(server);
// server.listen(8081);
// error handler
app.use(function(err, req, res, next) {
  // set locals, only providing error in development
  res.locals.message = err.message;
  res.locals.error = req.app.get('env') === 'development' ? err : {};

  // render the error page
  res.status(err.status || 500);
  res.send(err.status);
});

module.exports = app;

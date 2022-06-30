var express = require('express');
var router = express.Router();
var mongoose = require('mongoose');
var {ObjectId} = require('mongodb');

var app = express();
var server = require('http').createServer(app);
var io = require('socket.io')(server);
var Chat = require('../models/Chat.js');
var RoomUser = require('../models/RoomUser.js');
var Room = require('../models/Room.js');

// Socket IO
server.listen(8081);

io.on('connection', function (socket) {
  console.log('User connected');
  socket.on('disconnect', function() {
    console.log('User disconnected');
  });
  socket.on('save-message', function (data) {
    console.log("data1");
    console.log(data);
    io.emit('new-message', { message: data });
  });
});

/* GET ALL CHATS */
router.get('/:roomid', function(req, res, next) {
  Chat.find({ room: req.params.roomid }, function (err, products) {
    if (err) return next(err);
    res.json(products);
  });
});

/* GET SINGLE CHAT BY ID */
router.get('/:id', function(req, res, next) {
  Chat.findById(req.params.id, function (err, post) {
    if (err) return next(err);
    res.json(post);
  });
});

/* SAVE CHAT */
router.post('/', function(req, res, next) {
  Chat.create(req.body, function (err, post) {
    if (err) return next(err);
    res.json(post);
  });
});

router.post('/joinRoom', async function(req, res, next) {
  //console.log(req.body);
  const roomData = await Room.aggregate([
    { $match: { _id:ObjectId(req.body.room)}}
  ]);   
  if(roomData[0]!= undefined && roomData[0]!= null){
      var objectData = req.body;
      objectData.room_id = req.body.room
      objectData.room_name = roomData[0].room_name
       RoomUser.create(objectData, function (err, post) {
        if (err){
          return res.status(404).json({"roomData":{},"status":false,"statusCode":200,'message':err})
        } else {
          Chat.create(req.body, function (err, post) {
            if (err) return next(err);
            return res.status(200).json({"roomData":post,"status":false,"statusCode":200,'message':'joined sucessfully!'})
          });
        }
         //res.json(post);
      });
    
  }else {
    return res.status(404).json({"roomData":{},"status":false,"statusCode":200,'message':'No room found!'})
  }
});

/* UPDATE CHAT */
router.put('/:id', function(req, res, next) {
  Chat.findByIdAndUpdate(req.params.id, req.body, function (err, post) {
    if (err) return next(err);
    res.json(post);
  });
});

/* DELETE CHAT */
router.delete('/:id', function(req, res, next) {
  Chat.findByIdAndRemove(req.params.id, req.body, function (err, post) {
    if (err) return next(err);
    res.json(post);
  });
});

module.exports = router;

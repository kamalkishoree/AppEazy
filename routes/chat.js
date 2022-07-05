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

router.post('/sendMessage', async function(req, res, next) {
console.log(req.body);
  const userData = await RoomUser.aggregate([
    { $match: { room_id:ObjectId(req.body.room_id), user_id:String(req.body.user_id)}}
  ]);   
  if(userData.length >= 1){

    var objectData = req.body;
    objectData.room = roomData[0].room_name;
    objectData.vendor_id = roomData[0].vendor_id;
    objectData.vendor_user_id = roomData[0].vendor_user_id;
    objectData.order_user_id = roomData[0].order_user_id;
    objectData.order_vendor_id = roomData[0].order_vendor_id;

    objectData.to_id = roomData[0].vendor_id;
    objectData.from_id = roomData[0].order_user_id;
    objectData.from_user_id = req.body.user_id;

    if(objectData.user_type == 'vendor') {
      objectData.to_id = roomData[0].order_user_id;
      objectData.from_id = roomData[0].vendor_id;
      objectData.from_user_id = roomData[0].vendor_user_id;
    }
    Chat.create(objectData, function (err, post) {
      if (err) return next(err);
      res.json(post);
    });
  }
 
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

router.post('/joinRoomByID', async function(req, res, next) { 
  //console.log(req.body);
  const roomData = await Room.aggregate([
    { $match: { _id:ObjectId(req.body.room_id)}}
  ]);   
  if(roomData[0]!= undefined && roomData[0]!= null){
      var objectData = req.body;
      objectData.room_name = roomData[0].room_name;
      objectData.vendor_id = roomData[0].vendor_id;
      objectData.vendor_user_id = roomData[0].vendor_user_id;
      objectData.order_user_id = roomData[0].order_user_id;
      objectData.order_vendor_id = roomData[0].order_vendor_id;
      if(objectData.user_type == 'vendor') {
        objectData.user_id = roomData[0].vendor_id;
      }

      const userData = await RoomUser.aggregate([
        { $match: { room_id:ObjectId(req.body.room_id), user_id:objectData.user_id}}
      ]);   
      if(userData.length == 0){
          
        RoomUser.create(objectData, function (err, roomPost) {
          if (err){
            return res.status(404).json({"roomData":{},"status":false,"statusCode":200,'message':err})
          } else {
            Chat.create(req.body, function (err, post) {
              if (err) return next(err);
              return res.status(200).json({"roomData":post,"RoomUser":roomPost,"status":false,"statusCode":200,'message':'joined sucessfully!'})
            });
          }
           //res.json(post);
        });
        //return res.status(200).json({"roomData":[],"status":false,"statusCode":200,'message':'error!'})
      }else {
        var objectRoomData = req.body;
        objectRoomData.room = req.body.room_id;
       
          Chat.create(objectRoomData, function (err, post) {
            if (err) return next(err);
            return res.status(200).json({"roomData":post,"RoomUser":userData[0],"status":true,"statusCode":200,'message':'already exist!'})
          });
          

        //return res.status(200).json({"roomData":[],"status":false,"statusCode":200,'message':'error!'})
      }
      //objectData.room_name = roomData[0].room_name; 
    
    
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

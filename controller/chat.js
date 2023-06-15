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
const {
  getActiveUser,
  exitRoom,
  newUser,
  getIndividualRoomUsers
} = require('../Helper/helper');
// Socket IO
server.listen(8081);

var so = '';
io.on('connection', function (socket) {
 ////console.log('User connected');
  so = socket;
  socket.on('disconnect', function() {
   ////console.log('User disconnected');
  });

  socket.on('joinRoom', (data) => {
    const user = newUser(socket.id, data.email, data.roomId);
    socket.join(user.roomId);
    io.to(user.room).emit('roomUsers', {
      room: user.room,
      users:user.room
    });
  });
  socket.on('save-message', function (data) {
    io.emit('new-message', { message: data });
    io.emit('new-app-message', { message: data });
    //io.to(data.chatData._id).emit('new-message', { message: data });
  });
  socket.on('created', function (data) {
  });
});

/* GET ALL CHATS */
const getChatByRoomId = (async(req, res,next) => {
  Chat.find({ room: req.params.roomid }, function (err, products) {
    if (err) return next(err);
    res.json(products);
  });
});

/* GET SINGLE CHAT BY ID */
const getChatById = (async(req, res,next) => {
  Chat.findById(req.params.id, function (err, post) {
    if (err) return next(err);
    res.json(post);
  });
});

/* SAVE CHAT */
const saveChat = (async(req, res,next) => {
  Chat.create(req.body, function (err, post) {
    if (err) return next(err);
    res.json(post);
  });
});

const sendMessage = (async(req, res,next) => {
 ////console.log(req.body);
 try{
      var userData;
      var objectData = req.body;
      if(objectData.user_type == 'vendor') {
        objectData.display_image = objectData.display_image.original;
        userData = await RoomUser.aggregate([
          { $match: { room_id:ObjectId(req.body.room_id), vendor_user_id:String(req.body.user_id)}}
        ]);   
      } else {
        objectData.display_image = objectData.display_image.original;
        userData = await RoomUser.aggregate([
          { $match: { room_id:ObjectId(req.body.room_id), user_id:String(req.body.user_id)}}
        ]);  
      }
      //console.log(userData[0]);
      if(userData.length > 0){

        objectData.room = userData[0].room_id;
        objectData.room_name = userData[0].room_name;
        objectData.vendor_id = userData[0].vendor_id;
        objectData.vendor_user_id = userData[0].vendor_user_id;
        objectData.order_user_id = userData[0].order_user_id;
        objectData.order_vendor_id = userData[0].order_vendor_id;

        objectData.to_id = userData[0].vendor_id;
        objectData.from_id = userData[0].order_user_id;
        objectData.from_user_id = req.body.user_id;

        if(objectData.user_type == 'vendor') {
          objectData.to_id = userData[0].order_user_id;
          objectData.from_id = userData[0].vendor_id;
          objectData.from_user_id = userData[0].vendor_user_id;
        }

      //////console.log(objectData);
        Chat.create(objectData, function (err, chatD) {
          if (err){
            return res.status(200).json({"chatData":{},"status":false,"statusCode":200,'message':'No room found!'})
          }
          //res.json(post);
          return res.status(200).json({"chatData":chatD,"status":true,"statusCode":200,'message':'sent!'})
        });
      }else {
        return res.status(200).json({"chatData":{},"status":false,"statusCode":200,'message':'No room found!'})
      }
  } catch (err) {
    return res.status(200).json({"chatData":{},"status":true,"error":err,"statusCode":200})
  }
 
});

const joinRoom = (async(req, res,next) => {
  //console.log(req.body);
  try{
      const roomData = await Room.aggregate([
        { $match: { _id:ObjectId(req.body.room)}}
      ]);   
      if(roomData[0]!= undefined && roomData[0]!= null){
          var objectData = req.body;
          objectData.room_id = req.body.room
          objectData.room_name = roomData[0].room_name
          RoomUser.create(objectData, function (err, post) {
            if (err){
              return res.status(200).json({"roomData":{},"status":false,"statusCode":200,'message':err})
            } else {
              Chat.create(req.body, function (err, post) {
                if (err) return next(err);
                return res.status(200).json({"roomData":post,"status":false,"statusCode":200,'message':'joined sucessfully!'})
              });
            }
            //res.json(post);
          });
        
      }else {
        return res.status(200).json({"roomData":{},"status":false,"statusCode":200,'message':'No room found!'})
      }
  } catch (err) {
    return res.status(200).json({"roomData":{},"status":true,"error":err,"statusCode":200})
  }
});

const joinRoomByID = (async(req, res,next) => {
  //console.log(req.body);
  try{
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
                return res.status(200).json({"roomData":{},"status":false,"statusCode":200,'message':err})
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
        return res.status(200).json({"roomData":{},"status":false,"statusCode":200,'message':'No room found!'})
      }
    } catch (err) {
      return res.status(200).json({"roomData":{},"status":true,"error":err,"statusCode":200})
    }
});

const sendMessageJoin = (async(req, res,next) => {
  console.log(req.body);
 try {
      const roomData = await Room.aggregate([
        { $match: { _id:ObjectId(req.body.room_id)}}
      ]);   
      let date_obj = new Date();
    
      if(roomData[0]!= undefined && roomData[0]!= null){
          var objectData = req.body;
          var userData;
          objectData.room_name = roomData[0].room_name;
          objectData.vendor_id = roomData[0].vendor_id;
          objectData.vendor_user_id = roomData[0].vendor_user_id;
          objectData.order_user_id = roomData[0].order_user_id;
          objectData.order_vendor_id = roomData[0].order_vendor_id;
          objectData.auth_user_id = req.body.user_id;
          if(objectData.user_type == 'vendor') {
            objectData.user_id = roomData[0].vendor_id;
          }


          if(objectData.user_type == 'vendor') {
            objectData.display_image = objectData.display_image;
            userData = await RoomUser.aggregate([
              { $match: { room_id:ObjectId(req.body.room_id),user_type:String(objectData.user_type), vendor_user_id:String(req.body.vendor_user_id)}}
            ]);   
          } else if(objectData.user_type == 'admin') {
            objectData.display_image = objectData.display_image;
            userData = await RoomUser.aggregate([
              { $match: { room_id:ObjectId(req.body.room_id), user_type:String(objectData.user_type),email:String(req.body.email)}}
            ]);  
          
          } else if(objectData.user_type == 'agent') {
          
            objectData.display_image = objectData.display_image;
            userData = await RoomUser.aggregate([
              { $match: { room_id:ObjectId(req.body.room_id), user_type:String(objectData.user_type),auth_user_id:String(req.body.auth_user_id)}}
            ]);  
            //////console.log(userData);
            // return false;
          
          } else {
            objectData.display_image = objectData.display_image;
            userData = await RoomUser.aggregate([
              { $match: { room_id:ObjectId(req.body.room_id), user_type:String(objectData.user_type),user_id:String(req.body.order_user_id)}}
            ]);  
          }
          
          var roomDataRes = {};
          if(userData.length == 0){
            var check = await RoomUser.aggregate([
              { $match: { room_id:ObjectId(req.body.room_id)}},
            ]); 
          ////console.log('no',check);
            
            RoomUser.create(objectData, async function (err, roomPost) {
              if (err){
                return res.status(200).json({"roomData":{},"status":false,"statusCode":200,'message':err})
              } else {
              
                //if(userData.length > 0){
                  //io.emit('room-created', {'roomData' :roomPost,"status":true,"statusCode":200,'message':'sent!'});
                
                  objectData.room = roomPost.room_id;
                  objectData.room_name = roomPost.room_name;
                  objectData.vendor_id = roomPost.vendor_id;
                  objectData.vendor_user_id = roomPost.vendor_user_id;
                  objectData.order_user_id = roomPost.order_user_id;
                  objectData.order_vendor_id = roomPost.order_vendor_id;
              
                  objectData.to_id = roomPost.vendor_id;
                  objectData.from_id = roomPost.order_user_id;
                  objectData.from_user_id = req.body.user_id;
                  objectData.auth_user_id = roomPost.auth_user_id;
              
                  if(objectData.user_type == 'vendor') {
                    objectData.to_id = roomPost.order_user_id;
                    objectData.from_id = roomPost.vendor_id;
                    objectData.from_user_id = roomPost.vendor_user_id;
                  }
              
                //////console.log(objectData);
                  Chat.create(objectData, async function (err, chatD) {
                    if(check.length == 0){
                      const roomData1 = await Room.aggregate([
                        { $match: { _id:ObjectId(req.body.room_id)}},
                        { $lookup:
                          {
                            from: 'chats',
                            localField: '_id',
                            foreignField: 'room',
                            as: 'chat_Data'
                          }
                        },
                        { "$addFields": {
                          "chat_Data": { "$slice": ["$chat_Data", -1] }
                        }},
                      ]);   
                    //console.log('here');
                      io.emit('room-created', {'roomData' :roomData1,"status":true,"statusCode":200,'message':'sent!'});
                    } 
                    if (err){
                      return res.status(200).json({"chatData":{}, 'roomData' :roomDataRes , "status":false,"statusCode":200,'message':'No room found!'})
                    }
                    
                    await  Room.findOneAndUpdate({ _id: req.body.room_id}, { $set:{updated_date:new Date()} })
                    .then(async res => {
                    ////console.log('308');
                      //console.log(res)
                      roomDataRes = await res;
                      //console.log('ddd',roomDataRes );

                    })
                    .catch(err => {
                    ////console.log(err)
                    })
                    
                    //console.log(roomDataRes , '261');
                    //Room.updateOne({ _id:ObjectId(req.body.room_id)}, { $set:{updated_date:date_obj}})
                  //////console.log(io);
                    //io.emit('save-message', {"chatData":chatD,"status":true,"statusCode":200,'message':'sent!'});
                    //res.json(post);
                    return res.status(200).json({"chatData":chatD,'roomData' :roomDataRes,'new':true,"status":true,"statusCode":200,'message':'sent!'})
                  });
              
              }
            
            });
        
          }else {
            var objectRoomData = req.body;
            objectRoomData.room = req.body.room_id;
          
            //if(userData.length > 0){
      
              objectData.room = userData[0].room_id;
              objectData.room_name = userData[0].room_name;
              objectData.vendor_id = userData[0].vendor_id;
              objectData.vendor_user_id = userData[0].vendor_user_id;
              objectData.order_user_id = userData[0].order_user_id;
              objectData.order_vendor_id = userData[0].order_vendor_id;
          
              objectData.to_id = userData[0].vendor_id;
              objectData.from_id = userData[0].order_user_id;
              objectData.from_user_id = req.body.user_id;
          
              if(objectData.user_type == 'vendor') {
                objectData.to_id = userData[0].order_user_id;
                objectData.from_id = userData[0].vendor_id;
                objectData.from_user_id = userData[0].vendor_user_id;
              }
            
              Chat.create(objectData, async function (err, chatD) {
                if (err){
                  return res.status(200).json({"chatData":{}, 'roomData' :roomDataRes,"status":false,"statusCode":200,'message':'No room found!'})
                }
              await  Room.findOneAndUpdate({ _id: req.body.room_id}, { $set:{updated_date:new Date()} })
                .then(async res => {
                ////console.log('308');
                  //console.log(res)
                  roomDataRes = await res;
                  //console.log('ddd',roomDataRes );

                })
                .catch(err => {
                ////console.log(err)
                })
              
                //console.log(roomDataRes['vendor_to_user'] );
              ////console.log('fff');
                //io.emit('room-created', {"roomData":roomDataRes,"status":true,"statusCode":200,'message':'sent!'});

                return res.status(200).json({"chatData":chatD, 'new':true,'roomData' :roomDataRes ,"status":true,"statusCode":200,'message':'sent!'})
              });
            // }else {
          
          }
        
        
      }else {
        return res.status(200).json({"roomData":{},"status":false,"statusCode":200,'message':'No room found!'})
      }
    } catch (err) {
      return res.status(200).json({"roomData":{},"status":true,"error":err,"statusCode":200})
    }
});

/* UPDATE CHAT */
const updateChat = (async(req, res,next) => {
  Chat.findByIdAndUpdate(req.params.id, req.body, function (err, post) {
    if (err) return next(err);
    res.json(post);
  });
});

/* DELETE CHAT */
const deleteChat = (async(req, res,next) => {
  Chat.findByIdAndRemove(req.params.id, req.body, function (err, post) {
    if (err) return next(err);
    res.json(post);
  });
});

/*  FETCH ROOM USER BY ROOM ID */
const getRoomUserByRoomId = (async(req, res,next) => {
  //console.log(req.body);
  try{ 
      const roomData = await RoomUser.aggregate([
        { $match: { room_id:ObjectId(req.params.roomId),status:true}}
      ]);   
      if(roomData[0]!= undefined && roomData[0]!= null){

          return res.status(200).json({"userData":roomData,"status":true,"statusCode":200,'message':'User Found!'})

      }else {
          return res.status(200).json({"userData":{},"status":false,"statusCode":200,'message':'No user found!'})
      }
    } catch (err) {
      return res.status(200).json({"userData":{},"status":true,"error":err,"statusCode":200})
    }
  
});

module.exports = {
  getChatByRoomId,
  getChatById,
  saveChat,
  sendMessage,
  joinRoom,
  updateChat,
  deleteChat,
  getRoomUserByRoomId,
  joinRoomByID,
  sendMessageJoin
};

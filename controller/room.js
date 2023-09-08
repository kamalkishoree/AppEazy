var express = require('express');
var router = express.Router();
var mongoose = require('mongoose');
var Room = require('../models/Room.js');
var app = express();

var server = require('http').createServer(app);
var io = require('socket.io')(server);

/* GET ALL ROOMS */
const getRooms = ((req, res,next) => {
      Room.find(function (err, products) {
        if (err) return next(err);
        return res.json(products);
      });
})

const fetchRoomByClient = (async(req, res) => {
  try {
      const sub_domain = req.body.sub_domain;
      const client_id = req.body.client_id;
      const db_name = req.body.db_name;
      const user_id = req.body.user_id;
      const type = req.body.type;
      const group = await Room.aggregate([
        { $match: { db_name:db_name ,type:type,user_id: user_id ,client_id:String(client_id)}}
        //{ $match: { db_name:db_name ,vendor_id:'16',client_id:String(client_id)}}
      ]);    
      if(!group){
          return res.status(200).json({"roomData":{},"status":false,"statusCode":200})
      }else{
          return res.status(200).json({"roomData":group,"status":true,"statusCode":200})
      }
    } catch (err) {
      return res.status(200).json({"roomData":{},"status":true,"error":err,"statusCode":200})
    }
});

const fetchAllRoom = (async(req, res,next) => {
  try {
      const sub_domain = req.body.sub_domain;
      const client_id = req.body.client_id;
      const db_name = req.body.db_name;
      const user_id = req.body.user_id;
      const type = req.body.type;
      const group = await Room.aggregate([
        { $match: { db_name:db_name ,type:type,client_id:String(client_id)}},
        { $lookup:
          {
            from: 'roomusers',
            localField: '_id',
            foreignField: 'room_id',
            as: 'user_Data'
          }
        },

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
    
      { "$sort": { "updated_date" : -1 } } 
        //{ $match: { db_name:db_name ,vendor_id:'16',client_id:String(client_id)}}
      ]);    
      if(!group){
          return res.status(200).json({"roomData":{},"status":false,"statusCode":200})
      }else{
          return res.status(200).json({"roomData":group,"status":true,"statusCode":200})
      }
  } catch (err) {
      return res.status(200).json({"roomData":{},"status":true,"error":err,"statusCode":200})
  }
});


const fetchRoomByVendor = (async(req, res,next) => {

  try {
    const sub_domain = req.body.sub_domain;
    const client_id = req.body.client_id;
    const db_name = req.body.db_name;
    const vendor_id = req.body.vendor_id;
    const type = req.body.type;
    //var obj = ['16','17'];
    var v_id = vendor_id.map(function(item) {
      return String(item);
    });
   
    const group = await Room.aggregate([
      { $match: { vendor_id:{$in:v_id},db_name:db_name ,client_id:String(client_id),type:type}},
      { $lookup:
        {
          from: 'roomusers',
          localField: '_id',
          foreignField: 'room_id',
          as: 'user_Data'
        }
      },

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
   
    { "$sort": { "updated_date" : -1 } } 
      //{ $match: { db_name:db_name ,type:type,user_id: user_id ,client_id:String(client_id)}}
      //{ $match: { db_name:db_name ,client_id:String(client_id) , vendor_id:{$in:['16']}}}
    ]);    
      if(!group){
          return res.status(200).json({"roomData":{},"status":false,"statusCode":200})
      }else{
          return res.status(200).json({"roomData":group,"status":true,"statusCode":200})
      }
    } catch (err) {
      return res.status(200).json({"roomData":{},"status":false,"error":err,"statusCode":200})
    }
  
});

const fetchRoomByUserId = (async(req, res,next) => {

  try {
  //console.log(req.body);
    const sub_domain = req.body.sub_domain;
    const client_id = req.body.client_id;
    const db_name = req.body.db_name;
    const order_user_id = req.body.order_user_id;
    const type = req.body.type;

    const group = await Room.aggregate([
      { $match: { order_user_id:String(order_user_id),db_name:String(db_name) ,type:String(type)}},
      //{ $match: { order_user_id:"174",db_name:"salesdemo" ,client_id:'1',type:"agent_to_user"}},
      { $lookup:
        {
          from: 'roomusers',
          localField: '_id',
          foreignField: 'room_id',
          as: 'user_Data'
        }
      },

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
   
    { "$sort": { "updated_date" : -1 } } 
      //{ $match: { db_name:db_name ,type:type,user_id: user_id ,client_id:String(client_id)}}
      //{ $match: { db_name:db_name ,client_id:String(client_id) , vendor_id:{$in:['16']}}}
    ]);    
    
        if(!group){
            return res.status(200).json({"roomData":{},"status":false,"statusCode":200})
        }else{
            return res.status(200).json({"roomData":group,"status":true,"statusCode":200})
        }
    } catch (err) {
      return res.status(200).json({"roomData":{},"status":false,"error":err,"statusCode":200})
    }
  // Room.find(function (err, products) {
  //   if (err) return next(err);
  //   res.json(products);
  // });
});


const fetchRoomByUserIdUserToUser = (async(req, res,next) => {
  try {
  //console.log(req.body);
    const sub_domain = req.body.sub_domain;
    const client_id = req.body.client_id;
    const db_name = req.body.db_name;
    const order_user_id = req.body.order_user_id;
    const type = req.body.type;
    const p2p_id = req.body.p2p_id;

    const group = await Room.aggregate([
      { $match: { db_name:String(db_name) ,type:String(type), $or: [{ p2p_id: String(p2p_id) }, { order_user_id: String(order_user_id) }]}},

      //{ $match: { order_user_id:String(order_user_id),db_name:String(db_name) ,type:String(type),$or: [ { p2p_id: { $lt: p2p_id } } ]}},
      //{ $match: { order_user_id:"174",db_name:"salesdemo" ,client_id:'1',type:"agent_to_user"}},
      { $lookup:
        {
          from: 'roomusers',
          localField: '_id',
          foreignField: 'room_id',
          as: 'user_Data'
        }
      },

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
   
    { "$sort": { "updated_date" : -1 } } 
      //{ $match: { db_name:db_name ,type:type,user_id: user_id ,client_id:String(client_id)}}
      //{ $match: { db_name:db_name ,client_id:String(client_id) , vendor_id:{$in:['16']}}}
    ]);    
    
        if(!group){
            return res.status(200).json({"roomData":{},"status":false,"statusCode":200})
        }else{
            return res.status(200).json({"roomData":group,"status":true,"statusCode":200})
        }
    } catch (err) {
      return res.status(200).json({"roomData":{},"status":false,"error":err,"statusCode":200})
    }
  // Room.find(function (err, products) {
  //   if (err) return next(err);
  //   res.json(products);
  // });
});

//
const createRoom = (async(req, res,next) => {
  console.log("req.body.room_name");
  console.log(req.body);
 try {
   const roomData = await Room.aggregate([
     { $match: { room_name:req.body.room_name}}
   ]);   
   if(roomData.length == 0){
     ////console.log("here");
       Room.create(req.body, function (err, post) {
         if (err){
           return res.status(200).json({"roomData":{},"status":false,"statusCode":200,'message':err})
         } else {
           ////console.log("here25262");
           //io.emit('room-created', {"roomData":post,"status":true,"statusCode":200,'message':'sent!'});
           return res.status(200).json({"roomData":post,"status":false,"statusCode":200,'message':'created sucessfully!'})
       
         }
         //res.json(post);
       });
     
   }else {
     return res.status(200).json({"roomData":roomData[0],"status":true,"statusCode":200,'message':'already exist!'})
   }
 } catch (err) {
   return res.status(200).json({"roomData":{},"status":false,"error":err,"statusCode":200})
 }
});

/* GET SINGLE ROOM BY ID */

const getSingleRoomById = (async(req, res,next) => {
  Room.findById(req.params.id, function (err, post) {
    if (err) return next(err);
    res.json(post);
  });
});

/* SAVE ROOM */
const saveRoom = (async(req, res,next) => {
  Room.create(req.body, function (err, post) {
    if (err) return next(err);
    res.json(post);
  });
});

/* UPDATE ROOM */
const updateRoom = (async(req, res,next) => {
  Room.findByIdAndUpdate(req.params.id, req.body, function (err, post) {
    if (err) return next(err);
    res.json(post);
  });
});

/* UPDATE ROOM */
const raiseAnIssue = (async(req, res,next) => {

    await  Room.findOneAndUpdate({ _id: req.params.id}, { $set:{isRaiseIssue:req.body.isRaiseIssue} })
    .then(async res => {
        res.json({status:true});

    })
    .catch(err => {
      res.json({status:true});  
    })
});




/* DELETE ROOM */
const deleteRoom = (async(req, res,next) => {
  Room.findByIdAndRemove(req.params.id, req.body, function (err, post) {
    if (err) return next(err);
    res.json(post);
  });
});

const fetchRoomByUserAgent = (async(req, res,next) => {
  try {
    console.log(req.body);
    const sub_domain = req.body.sub_domain;
    const client_id = req.body.client_id;
    const db_name = req.body.db_name;
    const agent_id = req.body.agent_id;
    const agent_db = req.body.agent_db;
    const type = req.body.type;
    const group = await Room.aggregate([
      { $match: { agent_id:String(agent_id),agent_db:String(agent_db),type:String(type)}},
      { $lookup:
        {
          from: 'roomusers',
          localField: '_id',
          foreignField: 'room_id',
          as: 'user_Data'
        }
      },

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
   
    { "$sort": { "updated_date" : -1 } } 
      //{ $match: { db_name:db_name ,type:type,user_id: user_id ,client_id:String(client_id)}}
      //{ $match: { db_name:db_name ,client_id:String(client_id) , vendor_id:{$in:['16']}}}
    ]);    
    //res.json(group);
    ////console.log(group);
    
      if(!group){
          return res.status(200).json({"roomData":{},"status":false,"statusCode":200})
      }else{
          return res.status(200).json({"roomData":group,"status":true,"statusCode":200})
      }
    } catch (err) {
      return res.status(200).json({"roomData":{},"status":false,"error":err,"statusCode":200})
    }
  
});

module.exports = {
  getRooms,
  fetchRoomByClient,
  fetchRoomByClient,
  fetchAllRoom,
  fetchRoomByVendor,
  fetchRoomByUserId,
  fetchRoomByUserIdUserToUser,
  getSingleRoomById,
  saveRoom,
  updateRoom,
  deleteRoom,
  fetchRoomByUserAgent,
  createRoom,
  raiseAnIssue
}

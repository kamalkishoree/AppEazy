var express = require('express');
var router = express.Router();
var mongoose = require('mongoose');
var Room = require('../models/Room.js');

/* GET ALL ROOMS */
router.get('/', function(req, res, next) {
  Room.find(function (err, products) {
    if (err) return next(err);
    res.json(products);
  });
});

router.post('/fetchRoomByClient', async function(req, res, next) {
  ///try {
 // console.log(req.body);
     const sub_domain = req.body.sub_domain;
    const client_id = req.body.client_id;
    const db_name = req.body.db_name;
    const user_id = req.body.user_id;
    const type = req.body.type;
    const group = await Room.aggregate([
      { $match: { db_name:db_name ,type:type,user_id: user_id ,client_id:String(client_id)}}
      //{ $match: { db_name:db_name ,vendor_id:'16',client_id:String(client_id)}}
    ]);    
    //res.json(group);
    console.log(group);
    if(!group){
        return res.status(404).json({"roomData":{},"status":false,"statusCode":200})
    }else{
        return res.status(200).json({"roomData":group,"status":true,"statusCode":200})
    }
    // } catch (err) {
    //     res.status(404)
    //         .send({
    //             message: err.message,
    //             statusCode:404
    //         });
    // }
});

router.post('/fetchAllRoom', async function(req, res, next) {
  ///try {
  console.log(req.body);
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
        return res.status(404).json({"roomData":{},"status":false,"statusCode":200})
    }else{
        return res.status(200).json({"roomData":group,"status":true,"statusCode":200})
    }
 
});


router.post('/fetchRoomByVendor', async function(req, res, next) {
  ///try {
  console.log(req.body.vendor_id);
    const sub_domain = req.body.sub_domain;
    const client_id = req.body.client_id;
    const db_name = req.body.db_name;
    const vendor_id = req.body.vendor_id;
    const type = req.body.type;
    //var obj = ['16','17'];
    var v_id = vendor_id.map(function(item) {
      return String(item);
    });
    console.log(sub_domain);
    console.log(type);
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
    //res.json(group);
    console.log(group);
    
      if(!group){
          return res.status(404).json({"roomData":{},"status":false,"statusCode":200})
      }else{
          return res.status(200).json({"roomData":group,"status":true,"statusCode":200})
      }
  
});

router.post('/fetchRoomByUserId', async function(req, res, next) {
  ///try {
  //console.log(req.body.vendor_id);
    const sub_domain = req.body.sub_domain;
    const client_id = req.body.client_id;
    const db_name = req.body.db_name;
    const order_user_id = req.body.order_user_id;
    const type = req.body.type;
    //var obj = ['16','17'];
    // var v_id = vendor_id.map(function(item) {
    //   return String(item);
    // });
    console.log(sub_domain);
    console.log(type);
    const group = await Room.aggregate([
      { $match: { order_user_id:String(order_user_id),db_name:db_name ,client_id:String(client_id),type:type}},
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
    console.log(group);
    //client_id: client_id,sub_domain: sub_domain ,type: type ,user_id: user_id ,
    // Room.find({ db_name:db_name ,type:type,user_id:user_id,client_id:client_id}, function (err, products) {
    //   if (err) return next(err);
    //   res.json(products);
    // }); 
    
        if(!group){
            return res.status(404).json({"roomData":{},"status":false,"statusCode":200})
        }else{
            return res.status(200).json({"roomData":group,"status":true,"statusCode":200})
        }
    // } catch (err) {
    //     res.status(404)
    //         .send({
    //             message: err.message,
    //             statusCode:404
    //         });
    // }
  // Room.find(function (err, products) {
  //   if (err) return next(err);
  //   res.json(products);
  // });
});
/* GET SINGLE ROOM BY ID */
router.get('/:id', function(req, res, next) {
  Room.findById(req.params.id, function (err, post) {
    if (err) return next(err);
    res.json(post);
  });
});

/* SAVE ROOM */
router.post('/', function(req, res, next) {
  console.log(req.body);
  console.log("ss");
  Room.create(req.body, function (err, post) {
    if (err) return next(err);
    res.json(post);
  });
});

router.post('/createRoom', async function(req, res, next) {
  //console.log(req.body);
  const roomData = await Room.aggregate([
    { $match: { room_name:req.body.room_name}}
  ]);   
  if(roomData.length == 0){
      Room.create(req.body, function (err, post) {
        if (err){
          return res.status(404).json({"roomData":{},"status":false,"statusCode":200,'message':err})
        } else {
          return res.status(200).json({"roomData":post,"status":false,"statusCode":200,'message':'created sucessfully!'})
       
        }
         //res.json(post);
      });
    
  }else {
    return res.status(200).json({"roomData":roomData[0],"status":true,"statusCode":200,'message':'already exist!'})
  }
});

/* UPDATE ROOM */
router.put('/:id', function(req, res, next) {
  Room.findByIdAndUpdate(req.params.id, req.body, function (err, post) {
    if (err) return next(err);
    res.json(post);
  });
});

/* DELETE ROOM */
router.delete('/:id', function(req, res, next) {
  Room.findByIdAndRemove(req.params.id, req.body, function (err, post) {
    if (err) return next(err);
    res.json(post);
  });
});

module.exports = router;

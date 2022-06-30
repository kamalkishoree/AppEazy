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
  Room.create(req.body, function (err, post) {
    if (err) return next(err);
    res.json(post);
  });
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

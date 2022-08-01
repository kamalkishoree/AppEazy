var mongoose = require('mongoose'), Schema = mongoose.Schema;

var RoomSchema = new mongoose.Schema({
  room_name: String,
  room_id:String,
  order_vendor_id:String,
  order_id:String,
  vendor_id:String,
  client_id:String,
  vendor_user_id:String,
  order_user_id:String,
  sub_domain:String,
  agent_id:String,
  agent_db:String,
  status:{ type: Boolean, default: 1 },
  type:{ type: String, default: 'general' },
  db_name:String,
  created_date: { type: Date, default: Date.now },
  updated_date: { type: Date, default: Date.now },

});

module.exports = mongoose.model('Room', RoomSchema);

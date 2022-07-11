var mongoose = require('mongoose'), Schema = mongoose.Schema;

var RoomUserSchema = new mongoose.Schema({
  email: String,
  room_name: String,
  room_id:String,
  order_vendor_id:String,
  order_id:String,
  vendor_id:String,
  client_id:String,
  vendor_user_id:String,
  order_user_id:String,
  sub_domain:String,
  display_image:String,
  user_type:String,
  room_id:{ type: Schema.Types.ObjectId, ref: 'Room' },
  room_name:String,
  user_id:String,
  username:String,
  status:{ type: Boolean, default: 1 },
  created_date: { type: Date, default: Date.now },
});

module.exports = mongoose.model('RoomUser', RoomUserSchema);

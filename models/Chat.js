var mongoose = require('mongoose'), Schema = mongoose.Schema;

var ChatSchema = new mongoose.Schema({
  room : { type: Schema.Types.ObjectId, ref: 'Room' },
  nickname: String,
  email: String,
  display_image: String,
  user_type:String,
  to_message:String,
  from_message:String,
  message: String,
  room_name:String,
  vendor_id: String,
  vendor_user_id: String,
  order_user_id: String,
  order_vendor_id: String,
  to_id: String,
  from_id: String,
  from_user_id: String,
  chat_type: String,
  username:String,
  phone_num:String,
  auth_user_id:String,
  created_date: { type: Date, default: Date.now },
});

module.exports = mongoose.model('Chat', ChatSchema);

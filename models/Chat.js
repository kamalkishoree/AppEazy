var mongoose = require('mongoose'), Schema = mongoose.Schema;

var ChatSchema = new mongoose.Schema({
  room : { type: Schema.Types.ObjectId, ref: 'Room' },
  nickname: String,
  email: String,
  display_image:{ type: String, default: "https://www.kindpng.com/picc/m/24-248253_user-profile-default-image-png-clipart-png-download.png" },
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
  phone_num:{ type: String, default: "N/A" },
  auth_user_id:String,
  agent_id:String,
  user: { type: Schema.Types.ObjectId },
  created_date: { type: Date, default: Date.now },
  isMedia: {type: Boolean, default: false},
  mediaUrl: {type: String, default: null},
  thumbnail: {type: String, default: null},
});

module.exports = mongoose.model('Chat', ChatSchema);

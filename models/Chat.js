var mongoose = require('mongoose'), Schema = mongoose.Schema;

var ChatSchema = new mongoose.Schema({
  room : { type: Schema.Types.ObjectId, ref: 'Room' },
  nickname: String,
  email: String,
  display_image: String,
  user_type:String,
  to_message:String,
  from_message:String,
  to_id:String,
  from_id:String,
  from_user_id:String,
  message: String,
  created_date: { type: Date, default: Date.now },
});

module.exports = mongoose.model('Chat', ChatSchema);

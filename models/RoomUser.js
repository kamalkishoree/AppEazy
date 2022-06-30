var mongoose = require('mongoose'), Schema = mongoose.Schema;

var RoomUserSchema = new mongoose.Schema({
  email: String,
  display_image:String,
  room_id:{ type: Schema.Types.ObjectId, ref: 'Room' },
  room_name:String,
  user_id:String,
  status:{ type: Boolean, default: 1 },
  created_date: { type: Date, default: Date.now },
});

module.exports = mongoose.model('RoomUser', RoomUserSchema);

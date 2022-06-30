var mongoose = require('mongoose'), Schema = mongoose.Schema;

var RoomSchema = new mongoose.Schema({
  room_name: String,
  client_id:String,
  user_id:String,
  sub_domain:String,
  status:{ type: Boolean, default: 1 },
  type:{ type: String, default: 'general' },
  db_name:String,
  created_date: { type: Date, default: Date.now },
});

module.exports = mongoose.model('Room', RoomSchema);

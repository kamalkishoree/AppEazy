var mongoose = require('mongoose'), Schema = mongoose.Schema;

var UserLocationSchema = new mongoose.Schema({
  agent_id: Number,
  short_code:  { type: String, default: '' },
  lat: String,
  lng:String,
  order_id:Number,
  status:{ type: String, default: '' },
  created_date: { type: Date, default: Date.now },
  updated_date: { type: Date, default: Date.now },
});

module.exports = mongoose.model('UserLocation', UserLocationSchema);

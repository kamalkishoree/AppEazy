var UserLocation = require('../models/UserLocation.js');
var {ObjectId} = require('mongodb');

const {
  getIO
} = require('../Helper/helper');


const saveAgentLocation = async (req, res, next) => {
  const objectData = {
    agent_id: req.body.agent_id,
    short_code: req.body.short_code,
    lat: req.body.lat,
    lng: req.body.lng,
    order_id: req.body.order_id,
    status: req.body.status,
  };

  try {
    const locD = await UserLocation.create(objectData);
    getIO().emit(`agent-location-${locD.agent_id}`, locD);

    res.status(200).json({
      data: locD,
      status: true,
      statusCode: 200,
      message: 'Location saved successfully',
    });
  } catch (err) {
    console.error(err);
    res.status(500).json({
      data: {},
      status: false,
      statusCode: 500,
      message: 'Internal server error',
    });
  }
};

const getAgentLocation = async (req, res, next) => {
  try {
    const latestLocation = await UserLocation.aggregate([
      {
        $match: {
          agent_id: Number(req.body.agent_id),
          order_id: Number(req.body.order_id),
        },
      },
      {
        $sort: { created_date: -1 }, 
      },
      {
        $limit: 1,
      },
    ]);

    if (latestLocation.length === 0) {
      // No matching record found
      return res.status(404).json({
        data: {},
        status: false,
        statusCode: 404,
        message: 'No matching records found',
      });
    }

    res.status(200).json({
      data: latestLocation[0], // Send the latest record
      status: true,
      statusCode: 200,
      message: 'Latest data read successfully',
    });
  } catch (err) {
    console.error(err);
    res.status(500).json({
      data: {},
      status: false,
      statusCode: 500,
      message: 'Internal server error',
    });
  }
};

module.exports = {
  saveAgentLocation,
  getAgentLocation
};

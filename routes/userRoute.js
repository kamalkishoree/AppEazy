const express = require('express')
const router = express.Router()

const  { 
    saveAgentLocation,
    getAgentLocation
  
} = require('../controller/user')

router.post('/saveAgentLocation', saveAgentLocation);
router.get('/getAgentLocation', getAgentLocation);



module.exports = router
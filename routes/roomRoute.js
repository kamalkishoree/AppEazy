const express = require('express')
const router = express.Router()

const  { 
    getRooms,
    fetchRoomByClient,
    fetchAllRoom,
    fetchRoomByVendor,
    fetchRoomByUserId,
    fetchRoomByUserIdUserToUser,
    createRoom,
    getSingleRoomById,
    saveRoom,
    updateRoom,
    deleteRoom,
    fetchRoomByUserAgent
} = require('../controller/room')


router.get('/',getRooms);
router.post('/fetchRoomByClient',fetchRoomByClient);
router.post('/fetchAllRoom',fetchAllRoom);
router.post('/fetchRoomByVendor',fetchRoomByVendor);
router.post('/fetchRoomByUserIdUserToUser',fetchRoomByUserIdUserToUser);
router.post('/fetchRoomByUserId', fetchRoomByUserId);
router.get('/:id', getSingleRoomById);
router.post('/createRoom', createRoom);
router.post('/', saveRoom);
router.put('/:id', updateRoom);
router.delete('/:id', deleteRoom);
router.post('/fetchRoomByUserAgent',fetchRoomByUserAgent)


module.exports = router
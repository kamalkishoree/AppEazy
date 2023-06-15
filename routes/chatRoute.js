const express = require('express')
const router = express.Router()

const  { 
    getChatByRoomId,
    getChatById,
    saveChat,
    sendMessage,
    joinRoom,
    updateChat,
    deleteChat,
    getRoomUserByRoomId,
    joinRoomByID,
    sendMessageJoin
} = require('../controller/chat')

router.get('/:roomid', getChatByRoomId)
router.get('/:id', getChatById)
router.post('/', saveChat)
router.post('/sendMessage', sendMessage)
router.post('/joinRoom', joinRoom)
router.put('/:id', updateChat)
router.delete('/:id', deleteChat)
router.get('/getRoomUser/:roomId', getRoomUserByRoomId)
router.post('/joinRoomByID', joinRoomByID)
router.post('/sendMessageJoin', sendMessageJoin)

module.exports = router
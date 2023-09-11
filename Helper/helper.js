
var express = require('express');

var app = express();
var server = require('http').createServer(app);
var io = require('socket.io')(server);
//console.log(io);
server.listen(8081);
const users = [];


io.on('connection', function (socket) {
console.log(socket.handshake.query);
socket.on('joinRoom', (data) => {
    const user = newUser(socket.id, data.email, data.roomId);
    socket.join(user.roomId);
    io.to(user.room).emit('roomUsers', {
      room: user.room,
      users:user.room
    });
  });
  socket.on('save-message', function (data) {
    io.emit('new-message', { message: data });
    io.emit('new-app-message', { message: data });
    //io.to(data.chatData._id).emit('new-message', { message: data });
  });
});

function getIO() {
  //console.log(io);
  return io;
}

// Join user to chat
function newUser(id, email, room) {
  const user = { id, email, room };

  users.push(user);

  return user;
}

// Get current user
function getActiveUser(id) {
  return users.find(user => user.id === id);
}

// User leaves chat
function exitRoom(id) {
  const index = users.findIndex(user => user.id === id);

  if (index !== -1) {
    return users.splice(index, 1)[0];
  }
}

// Get room users
function getIndividualRoomUsers(room) {
  return users.filter(user => user.room === room);
}

module.exports = {
  getIO,
  newUser,
  getActiveUser,
  exitRoom,
  getIndividualRoomUsers
};
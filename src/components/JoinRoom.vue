<template>
  <b-row>
    <b-col align-self="start">&nbsp;</b-col>
    <b-col cols="6" align-self="center">
      <h2>
        Join Room
        <b-link href="#/">(Room List)</b-link>
      </h2>
      <b-form @submit="onSubmit">
        <b-form-group id="fieldsetHorizontal"
                  horizontal
                  :label-cols="4"
                  breakpoint="md"
                  label="Enter Nickname">
        <b-form-input id="nickname" :state="state" v-model.trim="chat.nickname"></b-form-input>
        <b-form-group id="fieldsetHorizontal"
              horizontal
              :label-cols="4"
              breakpoint="md"
              label="Enter email">
        <b-form-input id="email" :state="state" v-model="chat.email"></b-form-input>
         <b-form-group id="fieldsetHorizontal"
              horizontal
              :label-cols="4"
              breakpoint="md"
              label="Enter user_id">
        <b-form-input id="user_id" :state="state" v-model="chat.user_id"></b-form-input>
        </b-form-group>
        <b-button type="submit" variant="primary">Join</b-button>
      </b-form>
    </b-col>
    <b-col align-self="end">&nbsp;</b-col>
  </b-row>
</template>

<script>

import axios from 'axios'
import * as io from 'socket.io-client'

export default {
  name: 'JoinRoom',
  data () {
    return {
      chat: {},
      socket: io('https://chat.royoorders.com')
    }
  },
  methods: {
    onSubmit (evt) {
      evt.preventDefault()
      this.chat.room = this.$route.params.id
      this.chat.message = this.chat.nickname + ' join the room'
      console.log(this.chat)
      axios.post(`https://chat.royoorders.com/api/chat/joinRoom`, this.chat)
      .then(response => {
        console.log(response);
        console.log("response");
        this.socket.emit('save-message', { room: this.chat.room, nickname: this.chat.nickname, message: 'Join this room', created_date: new Date() });
        this.$router.push({
          name: 'ChatRoom',
          params: { id: this.$route.params.id, nickname: response.data.roomData.nickname }
        })
      })
      .catch(e => {
        this.errors.push(e)
      })
    }
  }
}
</script>

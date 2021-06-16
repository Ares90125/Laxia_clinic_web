<template>
  <div class="main-in">
    <div class="chat">
      <div class="chat-content">
        <div class="chat-main--wrapper">
          <div class="chat-main" id="chat-main">
            <template v-for="(msg, index) in messages">
              <div v-if="!index || getDateString(messages[index-1].created_at) != getDateString(msg.created_at)" class="chat-date">{{ msg.created_at | formatDate }}</div>
              <div :class="{'other': msg.user_id == 1, 'me': msg.user_id != 1 }">
                <p class="avatar"><img src="/img/img.svg" width="45px"></p>
                <div>
                  <div>
                    <p v-if="!msg.is_file" class="message">{{ msg.message }}
                      <span v-if="!index || getUnixTimestamp(msg.created_at) - getUnixTimestamp(messages[index-1].created_at) > 60" class="chat-time">{{ msg.created_at | formatTime12 }}</span></p>
                    <div v-else class="message--file">
                      <img :src="msg.file_thumb_url" />
                      <span v-if="!index || getUnixTimestamp(msg.created_at) - getUnixTimestamp(messages[index-1].created_at) > 60" class="chat-time">{{ msg.created_at | formatTime12 }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </template>
          </div>
          <div class="chat-attachment--wrapper">
            <file-upload
              v-if="mailboxId"
              ref="messageFileUploadComponent"
              id="attachment-dropzone"
              :maxFiles="5"
              :uploadUrl="`/api/test/mailboxes/${this.mailboxId}/fileupload`"
              @file-upload-success="handleFileSaved"
              @queue-complete="handleMultiFilesQueueComplete"
            />
          </div>
        </div>
        <div class="chat-input">
          <textarea
            v-if="reservation"
            v-model="newMessage" ></textarea>
          <button class="add-file" @click="handleFileInput"><img src="/img/addFile.svg"></button>
          <button class="message-send" @click="handleSendMessage"><img src="/img/send.svg"></button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  layout: 'basic',

  data() {
    return {
        messages: [],
        mailboxId: undefined,
        reservation: undefined,
        form: {
            messages: {
            message: '',
            },
            files: [],
        },
        newMessage: '',
        newMessages: [],
        isSedning: false,
        lastMessage: undefined,
        lastMessageId: undefined,
        firebaseKey: undefined
    }
  },

  mounted() {

    this.mailboxId = this.$route.params.id;
    this.$store.dispatch('state/setIsLoading')

    return Promise.all([
      axios.get(`/api/test/mailboxes/${this.mailboxId}/reservation`),
      axios.get(`/api/test/mailboxes/${this.mailboxId}/messages`),
      axios.get(`/api/test/mailboxes/${this.mailboxId}/getFirebaseKey`)
    ]).then(([res1, res2, res3]) => {
      this.reservation = res1.data.reservation
      this.messages = res2.data.messages
      this.firebaseKey = res3.data.firebase_key

      this.registerFirebase()
      
      this.$nextTick(() => this.scrollToEnd());
    }).finally(() => {
      this.$store.dispatch('state/removeIsLoading')
    });
  },

  methods: {
    scrollToEnd() {
      setTimeout(() => {
        var container = this.$el.querySelector("#chat-main");
        container.scrollBy({ 
          top: container.scrollHeight, // could be negative value
          left: 0, 
          behavior: 'smooth'
        });
      }, 500)
    },

    handleChangeStatus(newStatus) {
      this.$swal({
        title: '本当に変更しますか？',
        icon: 'warning',
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          this.$store.dispatch('state/setIsLoading')
          axios.get(`/api/test/reservations/${this.reservation.id}/status/${newStatus}`)
            .then(res => {
              this.reservation = res.data.reservation
              this.$swal({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                title: '変更しました。',
                icon: 'success',
              })
              this.$store.dispatch('state/removeIsLoading')
            })
            .catch(error => {
              this.$swal({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                title: '変更できません。',
                icon: 'error',
              })
              this.$store.dispatch('state/removeIsLoading')
            })
        }
      })
    },

    handleSendMessage(e) {
      let fileCount = this.$refs.messageFileUploadComponent.getQueuedFiles()
      if (fileCount) {
        this.$refs.messageFileUploadComponent.processQueue();
      } else {
        this.sendMessage(this.newMessage)
        this.newMessage = ''
      }

      this.$nextTick(() => this.scrollToEnd());
    },

    handleSendFile() {
      this.$refs.messageFileUploadComponent.processQueue();
    },

    sendMessage(message, isFile = 0) {
        if (!message) return;

        this.newMessages.push({
            message: message,
            isFile: isFile
        })

        if (this.isSedning === false) {
            this.isSending = true;
            this.sendToTarget();
        }
    },

    sendToTarget() {
        if (this.newMessages.length === 0) {
            this.isSending = false;
            return;
        }
        let sendingMessage = this.newMessages[0];
        this.newMessages = this.newMessages.filter((item, index) => index > 0);

        let formMessage = {
            messages: {
                message: sendingMessage.message,
                is_file: sendingMessage.isFile,
            }
        }

        axios.post(`/api/test/mailboxes/${this.mailboxId}/messages`, formMessage)
            .then(res => {
                this.messages.push({
                  ...res.data.message,
                  is_mine: true
                })
                let pushMessage = {
                    message: {
                        mailbox_id: res.data.message.mailbox_id,
                        user_id: res.data.message.user_id,
                        message: res.data.message.message,
                        is_file: res.data.message.is_file,
                        is_mine: !res.data.message.is_mine,
                        created_at: res.data.message.created_at,
                    }
                }

                this.updateFirebase(this.reservation.clinic.firebase_key, pushMessage)
                
                this.$forceUpdate();

                let self = this;
                setTimeout(function() {
                    self.sendToTarget()
                }, 500);
        })
    },

    handleFileInput() {
      $('#attachment-dropzone').trigger('click')
    },

    getDateString(value) {
      if (value) {
        return value.substr(0, 10);
      }
      return value;
    },

    getUnixTimestamp(value) {
      if (value) {
        return this.$moment(value).unix()
      }
      return value;
    },

    handleFileSaved(fileUrl) {
      this.sendMessage(fileUrl, 1)
    },

    handleMultiFilesQueueComplete() {
        this.$refs.messageFileUploadComponent.removeAllFiles()
        if (this.newMessage) {
            this.sendMessage(this.newMessage)
            this.newMessage = ''
        }
    },

    registerSnapshot() {
        if (this.firebaseKey) {
        let self = this;
        this.snapShot = firestore.collection("users").doc(this.firebaseKey)
            .onSnapshot(function (docRef) {
                let to = docRef.data();
                if (!self.lastMessage) {
                  self.lastMessage = { ...to.message }
                }
               console.log(to) 
                if (!to.message) return;

                if (self.lastMessage && JSON.stringify(self.lastMessage) !== JSON.stringify(to.message) && parseInt(to.message.mailbox_id) === parseInt(self.mailboxId)) {
                    
                    if (to.message.user_id === self.reservation.clinic.user_id) {
                        self.messages.push({
                            is_mine: false,
                            message: to.message.message,
                            is_file: to.message.is_file,
                            file_thumb_url: to.message.is_file && `/storage${to.message.message}`,
                            created_at: to.message.created_at,
                        })

                        self.messages = self.messages.sort((a, b) => {
                            if (a.created_at > b.created_at) return 1;
                            if (a.created_at < b.created_at) return -1;
                            return 0;
                        })

                        self.lastMessage = { ...to.message }

                        self.$forceUpdate()
                        
                        self.scrollToEnd()
                    }
                }
            })
        }
    },

    updateFirebase(key, data) {
        if (!key) return;
        firestore.collection('users').doc(key)
            .update(data)
    },

    registerFirebase() {
      if (this.firebaseKey) {
        this.registerSnapshot()
        return;
      }
      let self = this
      firestore.collection('users').add({
        user_id: 'test',
        // name: this.user.name,
        // message_unread: 0,
        // system_message: ''
      })
      .then(function (docRef) {
        self.firebaseKey = docRef.id
        self.registerSnapshot()
        axios.post(`/api/test/mailboxes/${self.mailboxId}/updateFirebaseKey`, {
          firebase_key: docRef.id
        })
      })
    },
  },
}
</script>

<style scoped>
</style>

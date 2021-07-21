<template>
  <div class="header">
    <div class="hamburger" @click="onNavToggle"><span></span></div>
    <div class="header-left-con">
      <!-- <form > -->
        <!-- <input type="text" v-model="search_name" name="name" placeholder="名前・電話番号・予約IDで検索" @keyup='add_todo_keyup' @focus="focusSearch" @blur="unfocusSearch" />
        <button v-if="search_name" class="b-colse" @click="handleClose"><i class="bi bi-x"></i></button> -->
        <!-- <button  :class="{ focus_btn: focus_search }" @click="goPatient" >
          <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M8.25 14.25C11.5637 14.25 14.25 11.5637 14.25 8.25C14.25 4.93629 11.5637 2.25 8.25 2.25C4.93629 2.25 2.25 4.93629 2.25 8.25C2.25 11.5637 4.93629 14.25 8.25 14.25Z" stroke="#767676" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M15.7498 15.7498L12.4873 12.4873" stroke="#767676" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button> -->
      <!-- </form> -->
      <header-search-box />
    </div>
      
    <div class="header-right">
      <div class="news-alert">
        <a href="#" class="dropdown-toggle noti-dropdown-btn unread" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <svg width="18" height="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M14 6.66669C14 5.3406 13.4732 4.06883 12.5355 3.13115C11.5979 2.19347 10.3261 1.66669 9 1.66669C7.67392 1.66669 6.40215 2.19347 5.46447 3.13115C4.52678 4.06883 4 5.3406 4 6.66669C4 12.5 1.5 14.1667 1.5 14.1667H16.5C16.5 14.1667 14 12.5 14 6.66669Z" stroke="#7A779A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M10.4419 17.5C10.2954 17.7526 10.0851 17.9622 9.83212 18.1079C9.5791 18.2537 9.29224 18.3304 9.00026 18.3304C8.70828 18.3304 8.42142 18.2537 8.16841 18.1079C7.91539 17.9622 7.7051 17.7526 7.55859 17.5" stroke="#7A779A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </a>
        <div class="news-alert-list dropdown-menu">
          <a href="" class="news-alert-one">
            <p><img src="/img/img.svg"></p>
            <div>
              <p>新しい予約が入りました。</p>
              <span>1時間前</span>
            </div>
          </a>
          <a href="" class="news-alert-one">
            <p><img src="/img/img.svg"></p>
            <div>
              <p>キャンセルが入りました。</p>
              <span>1時間前</span>
            </div>
          </a>
          <a href="" class="news-alert-one">
            <p><img src="/img/img.svg"></p>
            <div>
              <p>キャンセルが入りました。</p>
              <span>1時間前</span>
            </div>
          </a>
          <a href="" class="news-alert-one">
            <p><img src="/img/img.svg"></p>
            <div>
              <p>キャンセルが入りました。</p>
              <span>1時間前</span>
            </div>
          </a>
          <a href="" class="news-alert-one">
            <p><img src="/img/img.svg"></p>
            <div>
              <p>キャンセルが入りました。</p>
              <span>1時間前</span>
            </div>
          </a>
        </div>
      </div>
      <div class="login-info">
        <a class="nav-link dropdown-toggle"
          href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
        >
          <img :src="user.photo_url || '/img/avatar-img.png'" class="rounded-circle profile-photo mr-1">
          {{ user.name }}
        </a>
        <div class="header-menu dropdown-menu">
          <router-link :to="{ name: 'resetpassword' }" active-class="active">
            アカウント設定
          </router-link>
          <a href="#" class="logout" @click.prevent="logout">ログアウト</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  data: () => ({
    appName: window.config.appName
  }),

  data(){
    return{
      search_name: '',
      focus_search: false,
    }
  },

  computed: mapGetters({
    user: 'auth/user'
  }), 

  methods: {
    async logout () {
      // Log out the user.
      await this.$store.dispatch('auth/logout')

      // Redirect to login.
      this.$router.push({ name: 'login' })
    },
    onNavToggle () {
      this.$utils.toggleSidebar()
    },
    handleClose(e){
      e.preventDefault();
      this.search_name = "";
    },
    focusSearch(){
      this.focus_search = true;
    },
    unfocusSearch(){
      this.focus_search = false;
    },
    add_todo_keyup(e){
      if (e.key == 'Enter') {
        this.goPatient();
      }
    },
    goPatient(){
      // e.preventDefault();
      this.$router.push({ name: 'patients.search', params: {search: this.search_name } }).catch(()=>{});
      this.search_name = "";
    }
  }
}
</script>

<style scoped>
.profile-photo {
  width: 2rem;
  height: 2rem;
  margin: -.375rem 0;
}
</style>

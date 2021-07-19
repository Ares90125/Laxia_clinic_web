<template>
  <div class="bg-blue-gray auth-wrapper login-wrapper auth">
    <div class="auth--wrapper">
      <div class="auth--form newpass-form">
        <form @submit.prevent="reset" @keydown="form.onKeydown($event)">
          <h2 class="auth--title new-pass-title">新しいパスワードを設定してください</h2>       
          <!-- Email -->
          <div class="form-group">
            <label class="col-form-label text-md-right">{{ $t('新しいパスワード') }}</label>
            <div class="pw-wrap">
              <input v-model="form.password" :class="{ 'is-invalid': form.errors.has('password') }" class="form-control" type="password" name="password" id="password" placeholder="6文字以上で入力してください">
              <i class="bi bi-eye-fill" id="togglePassword" @click="handleTogglePassword"></i>
              <has-error :form="form" field="password" />
            </div>
          </div>

          <!-- Password -->
          <div class="form-group">
            <label class="col-form-label text-md-right">{{ $t('再入力してください') }}</label>
            <div class="pw-wrap">
              <input v-model="form.repassword" :class="{ 'is-invalid': form.errors.has('repassword') }" class="form-control" type="password" name="repassword" id="repassword" placeholder="6文字以上で入力してください">
              <i class="bi bi-eye-fill" id="toggleRePassword" @click="handleReTogglePassword"></i>
              <has-error :form="form" field="repassword" />
            </div>
          </div>
          
          <input v-model="form.email" type="hidden" name="email">
          <input v-model="form.token" type="hidden" name="token">

          <div class="auth--btnwrap register-btn">
            <!-- <button class="btn btn-primary" @click="showModal">{{ $t('パスワードを変更') }}</button> -->
            <v-button :loading="form.busy">{{ $t('パスワードを変更') }}</v-button>
          </div>
        </form>
      </div>

      <form-modal
        ref="confirmPassword"
        id="password-modal"
        title=""
        class="modal reset-success-modal"
      >
        <div class="main-modal">
          <div class="auth--wrapper">
            <div class="auth-form">
              <h2 class="auth-title pass-changed">パスワードを更新しました！</h2>
              <div class="auth-title-tip">
                正常にあなたのパスワードが更新されました。<br>
                下のボタンからログイン画面に移動してログインしてください。
              </div>
            </div>
            <div class="auth-btn--wrapper pass-changed">
              <button class="btn btn-primary" @click="hideModal">{{ $t('ログイン画面からログインする') }}</button>
            </div>
          </div>
        </div>
      </form-modal>

    </div>
  </div>
</template>

<script>

import Form from 'vform'

export default {
  layout: 'basic',

  middleware: 'guest',

  metaInfo () {
    return { title: this.$t('ログイン') }
  },

  data: () => ({
    form: new Form({
      password: '',
      repassword : '',
      email : '',
      token : '',
    }),
    remember: false,
  }),

  mounted(){
    this.form.token=this.$route.params.token;
    this.form.email=this.$route.query.email;
  },

  methods: {
    handleTogglePassword(){
      const togglePassword = document.querySelector('#togglePassword');
      const password = document.querySelector('#password');
      const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
      password.setAttribute('type', type);
      togglePassword.classList.toggle('bi-eye-slash');
    },
    handleReTogglePassword(){
      const toggleRePassword = document.querySelector('#toggleRePassword');
      const repassword = document.querySelector('#repassword');
      const type = repassword.getAttribute('type') === 'password' ? 'text' : 'password';
      repassword.setAttribute('type', type);
      toggleRePassword.classList.toggle('bi-eye-slash');  
    },
    showModal() {
      this.$refs.confirmPassword.show();
    },
    hideModal() {
      this.$refs.confirmPassword.hide();
      this.$router.push({ name: 'login' });
    },

    async reset () {
      try{
        const { data } = await this.form.post('/api/user/password/reset')
        console.log(data);

        if(data.reset_flag == 'successed') this.$refs.confirmPassword.show();
      } catch(e) {
        if(e.response.status === 400) {
          alert(e.response.data.email);
        } else if(e.response.status !== 422) {
          alert('操作が失敗しました。');
        }
      }
    }
  }
}
</script>
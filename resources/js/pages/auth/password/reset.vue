<template>
  <div class="bg-blue-gray auth-wrapper login-wrapper">
    <div class="auth--wrapper">
      <h2 class="auth-title">新しいパスワードを設定してください</h2>
      <div class="auth-form">
        <form @submit.prevent="login" @keydown="form.onKeydown($event)">          
          <!-- Email -->
          <div class="form-group">
            <label class="col-form-label text-md-right">{{ $t('新しいパスワード') }}</label>
            <div>
              <input v-model="form.password" :class="{ 'custom-pw-is-invalid-invalid': form.errors.has('password') }" class="custom-pw-form-control" type="password" name="password" id="password" placeholder="6文字以上で入力してください">
              <i class="bi bi-eye-fill" id="togglePassword" @click="handleTogglePassword"></i>
              <has-error :form="form" field="password" />
            </div>
          </div>

          <!-- Password -->
          <div class="form-group">
            <label class="col-form-label text-md-right">{{ $t('再入力してください') }}</label>
            <div>
              <input v-model="form.rePassword" :class="{ 'custom-pw-is-invalid-invalid': form.errors.has('rePassword') }" class="custom-pw-form-control" type="password" name="re-password" id="re-password" placeholder="6文字以上で入力してください">
              <i class="bi bi-eye-fill" id="toggleRePassword" @click="handleReTogglePassword"></i>
              <has-error :form="form" field="rePassword" />
            </div>
          </div>
          
          <div class="auth-btn--wrapper mt-5">
            <button class="btn btn-primary" @click="showModal">{{ $t('パスワードを変更') }}</button>
          </div>
        </form>
      </div>

      <form-modal
        ref="confirmPassword"
        id="password-modal"
        title=""
        class="modal"
      >
        <div class="main-modal">
          <div class="auth--wrapper">
            <div class="auth-form">
              <h2 class="auth-title mb-6 mt-2" >パスワードを更新しました！</h2>
              <span class="auth-title-tip">
                正常にあなたのパスワードが更新されました。<br>
                下のボタンからログイン画面に移動してログインしてください。
              </span>
            </div>
            <div class="auth-btn--wrapper sent-btn-panel mb-2">
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
      rePassword : ''
    }),
    remember: false
  }),

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
      const rePassword = document.querySelector('#re-password');
      const type = rePassword.getAttribute('type') === 'password' ? 'text' : 'password';
      rePassword.setAttribute('type', type);
      toggleRePassword.classList.toggle('bi-eye-slash');  
    },
    showModal() {
      this.$refs.confirmPassword.show();
    },
    hideModal() {
      this.$refs.confirmPassword.hide();
    },
    async login () {
      // Submit the form.
      const { data } = await this.form.post('/api/clinic/login')

      // Save the token.
      this.$store.dispatch('auth/saveToken', {
        token: data.token,
        remember: this.remember
      })

      // Fetch the user.
      await this.$store.dispatch('auth/fetchUser')

      // Redirect home.
      this.$router.push({ name: 'reservations' })
    }
  }
}
</script>

<style scoped>
.auth-title {
    font-size: 24px;
    font-weight: 700;
    text-align: center;
    margin-bottom: 3rem;
    margin-top: 5rem;
}
.auth--wrapper .auth-form {
    padding: 0 0 50px;
}
.modal{
  align-items: center;
  top: 0;
}
* >>> .form-modal-header{
  display: none !important;
}
</style>>

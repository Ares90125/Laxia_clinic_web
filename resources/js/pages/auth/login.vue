<template>
  <div class="bg-blue-gray auth-wrapper login-wrapper">
    <div class="auth--wrapper">
      <div class="auth-form">
        <form @submit.prevent="login" @keydown="form.onKeydown($event)">
          <h2 class="auth-title">クリニックアカウントにログイン</h2>
          <!-- Email -->
          <div class="form-group">
            <label class="col-form-label text-md-right">{{ $t('メールアドレスもしくはID名') }}</label>
            <div>
              <input v-model="form.email" :class="{ 'is-invalid': form.errors.has('email'), 'is-login-valid': Object.keys(form.errors.errors).length > 0 && !form.errors.has('email')}" class="form-control" type="email" name="email" placeholder="例：XXX@example.com or ID">
              <i v-if="form.errors && form.errors.has('email')" class="i-login-invalid bi bi-exclamation-triangle-fill"></i>
              <i v-if="Object.keys(form.errors.errors).length > 0 && !form.errors.has('email')" class="i-login-valid bi bi-check-circle-fill"></i>
              <has-error :form="form" field="email" />
            </div>
          </div>
          <!-- Password -->
          <div class="form-group">
            <label class="col-form-label text-md-right">{{ $t('パスワード') }}</label>
            <div>
              <input v-model="form.password" :class="{ 'is-invalid': form.errors.has('password'), 'is-login-valid': Object.keys(form.errors.errors).length > 0 && !form.errors.has('password') }" class="custom-pw-form-control" type="password" name="password" id="password" placeholder="6文字以上で入力してください">
              <i class="bi bi-eye-fill" id="togglePassword" @click="handleTogglePassword"></i>
              <i v-if="form.errors && form.errors.has('password')" class="i-login-invalid passwd-invalid bi bi-exclamation-triangle-fill"></i>
              <i v-if="Object.keys(form.errors.errors).length > 0 && !form.errors.has('password')" class="i-login-valid passwd-valid bi bi-check-circle-fill"></i>
              <has-error :form="form" field="password" />
            </div>
          </div>
          <div class="row">
            <div class="col-12 d-flex justify-content-end auth-forgot">
              <router-link :to="{ name: 'password.request' }">
                パスワードを忘れた場合
              </router-link>
            </div>
          </div>

          <div class="auth-btn--wrapper">
            <v-button :loading="form.busy">{{ $t('ログイン') }}</v-button>
          </div>
        </form>
      </div>
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
      email: '',
      password: ''
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
.form-control.is-invalid{
  background-image : none;
}
</style>

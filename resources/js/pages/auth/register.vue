<template>
  <div class="bg-blue-gray auth-wrapper login-wrapper auth">
    <div class="auth--wrapper">
      <div class="auth--form">
        <form @submit.prevent="register" @keydown="form.onKeydown($event)">
          <h2 class="auth--title">新規クリニックアカウントの作成</h2>
          <!-- Name -->
          <!-- <div class="form-group">
            <label class="col-form-label text-md-right">{{ $t('クリニック名') }}</label>
            <div>
              <input v-model="form.name" :class="{ 'is-invalid': form.errors.has('name') }" class="form-control" type="text" name="name">
              <has-error :form="form" field="name" />
            </div>
          </div> -->

          <!-- Email -->
          <div class="form-group">
            <label class="col-form-label text-md-right">{{ $t('メールアドレス') }}</label>
            <div>
              <input v-model="form.email" :class="{ 'is-invalid': form.errors.has('email') }" class="form-control" type="email" name="email" placeholder="例：XXX@example.com or ID">
              <has-error :form="form" field="email" />
            </div>
          </div>

          <!-- Password -->
          <div class="form-group">
            <label class="col-form-label text-md-right">{{ $t('パスワード') }}</label>
            <div class="pw-wrap">
              <input v-model="form.password" :class="{ 'is-invalid': form.errors.has('password') }" class="form-control" type="password" name="password" id="password" placeholder="6文字以上で入力してください">
              <i class="bi bi-eye-fill" id="togglePassword" @click="handleTogglePassword"></i>
              <has-error :form="form" field="password" />
            </div>
          </div>

          <!-- Password Confirmation -->
          <!-- <div class="form-group">
            <label class="col-form-label text-md-right">{{ $t('パスワード確認') }}</label>
            <div>
              <input v-model="form.password_confirmation" :class="{ 'is-invalid': form.errors.has('password_confirmation') }" class="form-control" type="password" name="password_confirmation">
              <has-error :form="form" field="password_confirmation" />
            </div>
          </div> -->

          <div class="auth--btnwrap register-btn">
            <v-button :loading="form.busy">{{ $t('アカウントを作成') }}</v-button>
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
    return {

    }
  },

  data: () => ({
    form: new Form({
      name: '',
      email: '',
      password: '',
      password_confirmation: '',
    }),
    mustVerifyEmail: false
  }),

  methods: {
    handleTogglePassword(){
      const togglePassword = document.querySelector('#togglePassword');
      const password = document.querySelector('#password');
      const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
      password.setAttribute('type', type);
      togglePassword.classList.toggle('bi-eye-slash');
    },
    async register () {
      // Register the user.
      const { data } = await this.form.post('/api/clinic/register')

      // Must verify email fist.
      if (data.status) {
        this.mustVerifyEmail = true
      } else {
        // Log in the user.
        const { data: { token } } = await this.form.post('/api/clinic/login')

        // Save the token.
        this.$store.dispatch('auth/saveToken', { token })

        // Update the user.
        await this.$store.dispatch('auth/updateUser', { user: data })

        // Redirect home.
        this.$router.push({ name: 'reservations' })
      }
    }
  }
}
</script>

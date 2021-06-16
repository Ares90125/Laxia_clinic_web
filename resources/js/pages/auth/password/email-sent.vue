<template>
  <div class="bg-blue-gray auth-wrapper login-wrapper">
    <div class="auth--wrapper">
        <div class="auth-form">
          <h2 class="auth-title mb-6" >メールを送信しました</h2>
          <!-- Email -->
          <span class="auth-title-tip">
            <strong>xxx@example.com</strong> にメールを送信しましたので、新しいパスワードを設定してください。
              <br>メールが届かない場合は、入力したアドレスに間違いがあるか、登録したメールアドレスが違う可能性があります。
          </span>
        </div>
        <div class="auth-btn--wrapper sent-btn-panel">
            <!-- <button class="mr-3 btn2" @click="$router.go(-1)">{{ $t('もう一度試してみる') }}</button> -->
            <button class="mr-3 btn2" @click="goBack">{{ $t('もう一度試してみる') }}</button>
            <button class="btn btn-primary" @click="goLogin">{{ $t('ログイン画面に戻る') }}</button>
        </div>
    </div>
  </div>
</template>

<script>

export default {
  layout: 'basic',
  middleware: 'guest',

  metaInfo () {
    return { title: this.$t('パスワード再設定') }
  },

  methods: {
    goLogin(){
        this.$router.push({ name: 'login' })
    },
    goBack(){
        // this.$router.push({ name: 'register' })
        this.$router.push({ name: 'letter.sent' })
    }
  }
}
</script>

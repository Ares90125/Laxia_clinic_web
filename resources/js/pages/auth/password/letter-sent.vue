<template>
  <div class="bg-blue-gray auth-wrapper login-wrapper">
    <div class="auth--wrapper">
        <div class="email-form">
            <div class="d-flex justify-content-center">
                <img src="/img/email_mark.png" width="50">
            </div>
            <h2 class="auth-title mt-3" >リクエストを受け取りました</h2>
            <p class="auth-sub-title" >今すぐパスワードをリセツトできます。</p>
            <div class="auth-btn--wrapper sent-btn-panel mt-4">
                <button class="btn btn-danger mt-2" @click="goLogin">{{ $t('パスワードをリセツトする') }}</button>
            </div>
            <!-- Email -->
            <p class="auth-sub-title">
                パスワードをリセツトしてから24時間以内に, 新しいパスワードを 設定してください。
                24時間以を経過すると, 再度パスワードのリセツトをリクエストする必要がありますでご注意ください。
            </p>
            <p class="auth-sub-title mt-3">
                新しいパスワードのリクエストをした覚えがない場合は, このメールを無視していただいて結構です。
            </p>
            <p class="auth-sub-content mt-5">
                このメールをyoshiambition18@gmail.com宛てに送信されました <br>
                <strong>メー儿設定を管理する ・ 自分のアカウントではない</strong>
            </p>
            <p class="auth-sub-content mt-3">
                <strong>へルプセン夕ー ・ プライバシーポリシー ・ 利用規約</strong>
            </p>

            <div class="d-flex justify-content-center mt-5">
                <img src="/img/email_mark.png" width="50">
            </div>
            <h2 class="auth-title mt-3">パスワードをリセツトできませんでした</h2>
            <p class="auth-sub-title mt-3">
                このメールアドレスにはアカウントがありません。
            </p>
            <p class="auth-sub-title mt-3">
                別のメールで登録しましたか？そのアドレスでパスワードをリセットしてみてください
            </p>
            <div class="auth-btn--wrapper sent-btn-panel mt-4">
                <button class="btn btn-danger mt-2" @click="goLogin">{{ $t('パスワードを再設定する') }}</button>
            </div>
            <p class="auth-sub-title">
                新しいパスワードを要求しませんでしたか？このメールは無視してかまいません。
            </p>
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
        this.$router.push({ name: 'register' })
    }
  }
}
</script>
<style scoped>
.email-form{
    max-width: 550px;
    padding: 30px 0;
    margin: auto;
}
.auth-title{    
    font-size: 22px;
    font-weight: bold;
    text-align: center;
    margin-bottom: 1rem;
}
.auth-sub-title{
    font-size: 18px;
    text-align: center;
    font-weight: 500;
}
.auth-sub-content{
    font-size: 14px;
    text-align: center; 
}
.sent-btn-panel{
    margin-bottom: 2rem;
}
</style>

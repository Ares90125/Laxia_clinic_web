function page (path) {
  return () => import(/* webpackChunkName: '' */ `~/pages/${path}`).then(m => m.default || m)
}

export default [
  { path: '/', redirect: { name: 'welcome' } },

  { path: '/', name: 'welcome', component: page('welcome.vue') },

  { path: '/login', name: 'login', component: page('auth/login.vue') },
  { path: '/register', name: 'register', component: page('auth/register.vue') },
  { path: '/password/reset', name: 'password.request', component: page('auth/password/email.vue') },
  { path: '/password/reset/:token', name: 'password.reset', component: page('auth/password/reset.vue') },
  { path: '/email/verify/:id', name: 'verification.verify', component: page('auth/verification/verify.vue') },
  { path: '/email/resend', name: 'verification.resend', component: page('auth/verification/resend.vue') },

  { path: '/password/reset-sent', name: 'password.sent', component: page('auth/password/email-sent.vue') },
  { path: '/password/newset', name: 'password.newset', component: page('auth/password/reset.vue') },
  { path: '/password/letter-sent', name: 'letter.sent', component: page('auth/password/letter-sent.vue') },

  //　プロジェクト：20年08月07日
  { path: '/reservations', name: 'reservations', component: page('reservations/index.vue') },
  { path: '/mailboxes/:id', name: 'mailbox', component: page('mailbox/index.vue') },
  { path: '/payments', name: 'payments', component: page('payments/index.vue') },
  { path: '/history', name: 'history', component: page('history/index.vue') },
  { path: '/patients', name: 'patients', component: page('patients/index.vue') },
  { path: '/patients/:id', name: 'patient', component: page('patients/detail.vue') },
  { path: '/patients/:search', name: 'patients.search', component: page('patients/index.vue') },
  { path: '/clinic', name: 'clinic', component: page('clinic/index.vue') },
  { path: '/doctors', name: 'doctors', component: page('doctors/index.vue') },
  { path: '/menus', name: 'menus', component: page('menus/index.vue') },
  { path: '/cases', name: 'cases', component: page('cases/index.vue') },
  { path: '/billing', name: 'billing', component: page('billing/index.vue') },
  { path: '/resetpassword', name: 'resetpassword', component: page('resetpassword/index.vue') },


  // { path: '/home', name: 'home', component: page('home.vue') },
  // { path: '/settings',
  //   component: page('settings/index.vue'),
  //   children: [
  //     { path: '', redirect: { name: 'settings.profile' } },
  //     { path: 'profile', name: 'settings.profile', component: page('settings/profile.vue') },
  //     { path: 'password', name: 'settings.password', component: page('settings/password.vue') }
  //   ]
  // },

  { path: '*', component: page('errors/404.vue') },

  // テスト用
  { path: '/test/mailboxes/:id', name: 'test-mailbox', component: page('mailbox/test.vue') },
]

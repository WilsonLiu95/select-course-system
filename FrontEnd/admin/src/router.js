export default {
  base: __dirname,
  routes: [
    // ========================================PC管理系统========================================
    { path: '/', redirect: "/home" },
    { name: 'login', path: '/login', component: require('_views/login.vue') },
    { name: 'home', path: "/home", component: require('_views/home.vue') },
    { name: 'config', path: "/config", component: require('_views/config.vue') },
    { name: 'info', path: "/info", component: require('_views/info.vue') },
    { name: 'export', path: "/export", component: require('_views/export.vue') },
    { path: '*', component: require('_views/404.vue') }
  ]
}
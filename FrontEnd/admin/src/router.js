export default {
  base: __dirname,
  routes: [
    // ========================================PC管理系统========================================
    { path: '/', redirect: "/home" },
    { name: 'login', path: '/login', component: require('_views/login.vue') },
    { name: 'home', path: "/home", component: require('_views/home.vue') },
    { name: 'course', path: "/course", component: require('_views/course.vue') },
    { name: 'info', path: "/info", component: require('_views/info.vue') },
    { path: '*', component: require('_views/404.vue') }
  ]
}
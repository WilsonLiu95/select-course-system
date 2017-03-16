export default {
  base: __dirname,
  routes: [

    // ========================================微信端========================================
    // 功能区
    { path: '/', redirect: "/register" },
    { name: "register", path: '/register', component: require('_views/register.vue') },
    { name: "wechat", path: '/wechat', component: require('_views/wechat.vue') },

    // tab页面
    {
      path: '/tab',
      component: require('_views/tab/index.vue'), // 通过这个识别老师和学生
      children: [
        // 基本的四个
        { path: '', redirect: "course" }, // 重定向到默认的course
        { name: 'tab-course', path: 'course', component: require('_views/tab/course.vue') },
        { name: 'tab-account', path: 'account', component: require('_views/tab/account.vue') },
      ]
    },
    // 其他页面
    { name: "details", path: '/details/:direction_index/:course_index', component: require('_views/page/details.vue') },

    // 选择班级和方向
    { name: "select-class", path: '/select-class', component: require('_views/page/select-class.vue') },
    { name: "select-direction", path: '/select-direction', component: require('_views/page/select-direction.vue') },

    // 专业方向课程 选课与退选页面
    { name: "hanlde-course", path: '/course/(direction|common)/(select|quit)', component: require('_views/page/handle-course.vue') },

    // 公共选修课程 选课与退选页面
    // { name: "course", path: '/common-course/(select|quit)', component: require('_views/page/common-course.vue') },
    // 选课结果
    { name: "select-result", path: '/select-result', component: require('_views/page/select-result.vue') },
    { path: '*', component: require('_views/404.vue') }
  ]
}
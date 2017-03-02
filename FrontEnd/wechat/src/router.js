export default {
    base: __dirname,
    routes: [

        // ========================================微信端========================================
        // 功能区
        { path: '', redirect: "/register" },
        { name: "register", path: '/register', component: require('_views/register.vue') },
        { name: "wechat", path: '/wechat', component: require('_views/wechat.vue') },

        // tab页面
        {
            path: '/tab',
            component: require('_views/tab/index.vue'), // 通过这个识别老师和学生
            children: [
                // 基本的四个
                { path: '', redirect: "course" }, // 重定向到默认的course
                { name: 'course', path: 'course', component: require('_views/tab/course.vue') },
                { name: 'schedule', path: 'schedule', component: require('_views/tab/schedule.vue') },
                { name: 'account', path: 'account', component: require('_views/tab/account.vue') },
            ]
        },
        // 其他页面
        { name: "details", path: '/details/:course_id', component: require('_views/page/details.vue') },
        { name: "select-direction", path: '/select-direction', component: require('_views/page/select-direction.vue') },
        { name: "select-class", path: '/select-class', component: require('_views/page/select-class.vue') },

        { path: '*', component: require('_views/404.vue') }
    ]
}
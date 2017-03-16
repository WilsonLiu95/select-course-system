// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue';

import MintUI from 'mint-ui'
import 'mint-ui/lib/style.css'
import VueRouter from 'vue-router'
import axios from 'axios'

import routerConfig from './router'
import config from '../config'
import App from './App'
import { Indicator, Toast, MessageBox } from 'mint-ui'
import validator from 'validator'

Vue.use(VueRouter)
Vue.use(MintUI)

//开启debug模式
Vue.config.debug = true;

// ======================配置路由===============================
var router = new VueRouter(routerConfig)

// ======================配置mock数据和全局常量===============================
window._store = { // 因为是单页面框架，将全局变量当成session使用
  // course页面的数据
  page: '', // 用户在哪一页
  hasStoreCourse: false,
  course: [], // 存储课程数据
}
window.util = {
  v: validator,
  is(type, value, option) {
    if (value === undefined || value === null) {
      return false
    }
    var args = [].slice.call(arguments).slice(2);
    return validator[type](value, args)
  },

  toast: Toast,
  box: MessageBox,
}


// ======================配置HTTP请求===============================

// Add a request interceptor
axios.interceptors.request.use((config) => {
  if (!config.noIndicator) {
    Indicator.open({
      text: '请求中...',
      spinnerType: 'double-bounce'
    });
  }
  // Do something before request is sent

  return config;
}, (error) => {
  // Do something with request error
  return Promise.reject(error);
});

// Add a response interceptor

axios.interceptors.response.use((response) => {
  // 关闭弹窗
  if (!response.config.noIndicator) {
    Indicator.close();
  }
  return response;
}, (error) => {
  if (!error.response.config.noIndicator) {
    Indicator.close();
  }

  var res = error.response;
  if (res.status == 301) { // 前端控制跳转
    // 跳转提示
    if (res.data.url) {
      location.href = res.data.url
    } else {
      router.push(res.data.option)
    }
  } else if ([400, 422].indexOf(res.status) !== -1) { // 客户端请求错误，数据校验无问题
    // 前端的数据校验错误

  }

  // 弹出错误提示
  if (res.status == 422) {
    var message = '';
    for (var key in res.data) {
      message += res.data[key]
    }
  } else {
    message = res.data.msg
  }
  message ? util.toast({
    message: message,
    duration: 1500,
    position: 'top',
  }) : ""

  // Do something with response error
  return Promise.reject(error);
});


axios.defaults.baseURL = (process.env.NODE_ENV !== 'production' ? config.dev.httpUrl : config.build.httpUrl); // 同时根据不同环境引用不同的ajax请求前缀。
axios.defaults.withCredentials = true; // 本地dev开发时，存在跨域。跨域请求时，将不带上cookie。需要设置这个参数为true才会带上cookie。坑了几天。
axios.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded';
// phpstorm断点调试 需要此参数
// axios.defaults.params = {
//   XDEBUG_SESSION_START: "PHPSTORM"
// }
Vue.prototype.$http = axios
  /* eslint-disable no-new */
new Vue({
  router: router,
  render: h => h(App)
}).$mount('#app');
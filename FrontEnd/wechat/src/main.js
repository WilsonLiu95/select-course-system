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
  // account页面的数据
  account: {}, // 账户信息
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
  Indicator.close();


  return response;
}, (error) => {
  Indicator.close();
  var res = error.response;


  if (res.status == 301) { // 前端控制跳转
    if (res.data.url) {
      location.href = res.data.url
    } else {
      router.push(res.data.option)
    }
  }
  if (res.status == 400) { // 客户端请求错误，数据校验无问题
    util.toast({
      message: response.data.msg,
      duration: 2000
    })
  }
  if (res.status == 422) { // 前端的数据校验错误
    var message = '';
    for (var key in res.data) {
      message += res.data[key]
    }
    util.toast({
      message: message,
      duration: 2000
    })
  }
  // 关闭弹窗

  // Do something with response error
  return Promise.reject(error);
});


axios.defaults.baseURL = (process.env.NODE_ENV !== 'production' ? config.dev.httpUrl : config.build.httpUrl); // 同时根据不同环境引用不同的ajax请求前缀。
axios.defaults.withCredentials = true; // 本地dev开发时，存在跨域。跨域请求时，将不带上cookie。需要设置这个参数为true才会带上cookie。坑了几天。
axios.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded';
// phpstorm断点调试 需要此参数
axios.defaults.params = {
  XDEBUG_SESSION_START: "PHPSTORM"
}
Vue.prototype.$http = axios
  /* eslint-disable no-new */
new Vue({
  router: router,
  render: h => h(App)
}).$mount('#app');
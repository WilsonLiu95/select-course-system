// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'

import VueRouter from 'vue-router'
import axios from 'axios'

import ElementUI from 'element-ui'
import 'element-ui/lib/theme-default/index.css'

import routerConfig from './router'
import config from '../config'
import App from './App'
Vue.use(VueRouter)
Vue.use(ElementUI)

import { Loading, Message } from 'element-ui';
//开启debug模式
Vue.config.debug = true;

// ======================配置路由===============================
var router = new VueRouter(routerConfig)


// ======================配置HTTP请求===============================
var loading
  // Add a request interceptor
axios.interceptors.request.use(function(config) {

  // debugger
  // Do something before request is sent
  if (!config.noLoading) {
    loading = Loading.service({
      fullscreen: true,
      text: "请求中..."
    })
  }
  return config;
}, function(error) {
  // Do something with request error
  return Promise.reject(error);
});

// Add a response interceptor

axios.interceptors.response.use(function(response) {
  if (!config.noLoading) {
    loading.close();
  }
  if (response.data.msg) {
    Message({
      message: response.data.msg,
      type: "success" // 状态为0则为错误，其他都显示为成功
    })
  }

  return response;
}, function(error) {
  if (!error.response.config.noLoading) {
    loading.close();
  }
  var res = error.response;
  //================================422错误================================
  if (res.status === 422) { // 客户端请求错误，数据校验无问题
    // 前端的数据校验错误
    var message = '';
    for (var key in res.data) {
      message += res.data[key]
    }
    Message({
      message: message,
      type: "error" // 状态为0则为错误，其他都显示为成功
    })
  }
  // =================================前端控制跳转============================================
  if (res.status == 301) {
    // 跳转提示
    if (res.data.url) {
      location.href = res.data.url
    } else {
      router.push(res.data.option)
    }
    if (res.data.msg) {
      Message({
        message: res.data.msg,
        type: "success" // 状态为0则为错误，其他都显示为成功
      })
    }

  }
  //================================500错误================================
  if (res.status === 500 && error.message) {
    Message({
      message: error.message,
      type: "error" // 状态为0则为错误，其他都显示为成功
    })

  }
  if (res.status === 400 && res.data.msg) {
    Message({
      message: res.data.msg,
      type: "error" // 状态为0则为错误，其他都显示为成功
    })
  }
  // Do something with response error
  return Promise.reject(error);
});

axios.defaults.baseURL = (process.env.NODE_ENV !== 'production' ? config.dev.httpUrl : config.build.httpUrl); // 根据环境不同，配置不同的ajax请求前缀
axios.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded';
axios.defaults.withCredentials = true; // true才能使跨域 请求带上cookie
// phpstorm断点调试 需要此参数
axios.defaults.params = {
  XDEBUG_SESSION_START: "PHPSTORM"
}
Vue.prototype.$http = axios // 将axios绑定到vue上
  /* eslint-disable no-new */
new Vue({
  router: router,
  render: h => h(App)
}).$mount('#app');
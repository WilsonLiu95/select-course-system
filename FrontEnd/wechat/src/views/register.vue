<template>
  <div class="register-page">
    <div class='t-center'>
      <h4>第一步：账号绑定</h4>
      <h5>请如实填写个人信息，填写后不允许修改</h5>
    </div>
    <div>
      <mt-field label="姓名"
                placeholder="请输入姓名"
                :state="data.name ? 'success' : 'error'"
                v-model="data.name"></mt-field>
      <mt-field label="学号"
                placeholder="请输入学号"
                :state="data.job_num ? 'success' : 'error'"
                v-model="data.job_num"></mt-field>
      <mt-button size="large"
                 type="primary"
                 @click="register">确认</mt-button>
    </div>
  </div>
</template>
<script>
export default {
  name: "register",
  data() {
    return {
      data: {
        name: "刘盛",
        job_num: "1995",
      }
    }
  },
  created() {
    this.$http.get('register/is-login') // 判断是否已经登录，登录过则自动跳转
  },
  methods: {
    register() {
      var data = {
        name: this.data.name,
        job_num: this.data.job_num.toUpperCase() // 默认大小
      }
      // debugger
      if (data.name && data.job_num) { // 需要填写过
        this.$http.post("register", data)
      }
    }
  }
};

</script>

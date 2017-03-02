<template>
  <div class="register-page">
    <h5 style="text-align: center;">请如实填写个人信息，填写后不允许修改</h5>
    <div>

    </div>
  </div>
</template>
<script>
  export default {
    name: "select-class",
    data() {
      return {
        data: {

        }
      }
    },
    created() {
      this.$http.get('register/is-login') // 判断是否已经登录，登录过则自动跳转
    },
    methods: {


    },

  };

</script>

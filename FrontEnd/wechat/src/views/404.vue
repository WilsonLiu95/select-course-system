<template>
  <article class="view-404">
    <header>
      <span class="error-icon"></span>
      <h1>出错啦！</h1>
      <h2>页面被飞碟收走了！</h2>
    </header>
  </article>
</template>

<script>
  export default {
    name: "not-found",
    data() {
      return {

      }
    },
    created() {
      this.$http.get('/test').then() // 判断是否已经登录，登录过则自动跳转
    },
    

  };
</script>
<template>
  <div id="app">
    <el-menu theme="dark"
             mode="horizontal"
             :router="true">
      <el-menu-item index="./home">HUST专业课选课系统</el-menu-item> 
      <el-menu-item index="./home">主页</el-menu-item>
      <el-menu-item index="./info">院系信息</el-menu-item>
      <el-menu-item index="./course">课程</el-menu-item>
    </el-menu>
    <router-view>
    </router-view>
  </div>
</template>

<script>
export default {
  name: 'app',
  data() {
    return {
      institute: '电信学院',
    }
  },
};

</script>

<style>
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}
</style>

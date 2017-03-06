<template>
  <div class="index-page">
    <!--路由-->
    <router-view class="second-router">
    </router-view>
    <!--四栏tab-->
    <mt-tabbar v-model="selected" :fixed=true>
      <mt-tab-item id="course">
        <img slot="icon" :src="assets.class"> <span>课题</span>
      </mt-tab-item>
      <mt-tab-item id="start-select">
        <img slot="icon" :src="assets.schedule"> <span>选课</span>
      </mt-tab-item>

      <!--<mt-tab-item id="account">
        <img slot="icon" :src="assets.account"> <span>我的</span>
      </mt-tab-item>-->
    </mt-tabbar>
  </div>
</template>

<script>
  export default {
    name: "index",
    data() {
      return {
        // 引入资源
        assets: {
          class: require("assets/class.svg"),
          schedule: require("assets/schedule.svg"),
          account: require("assets/account.svg"),
        },
        selected: "course", // 默认课程页面
      }
    },
    watch: {
      selected: function (selected) {
        this.$router.push({ name: selected }) // 改变hash是为了重载该tab的组件，同时其他组件由于没有匹配路由规则被销毁
      }
    },
    created() {

      // 创建的时候监听路由变化，以编程方式响应跳转到相应的页面
      var hashArr = location.hash.split("/")
      this.selected = hashArr[2]


    },
    methods: {

    },

  };

</script>

<style scoped>
  .tab-page-container {
    padding-bottom: 50px;
  }

</style>

<template>
  <div class="tab-page-container">
    <mt-cell label="所选专业方向">{{dir}}</mt-cell>
    <mt-cell class='t-center' title="请确认以上信息，确认后开始选课。"></mt-cell>
    <mt-button size='large' type="primary">开始选课</mt-button>
  </div>
</template>
<script>
  export default {
    name: "schedule-tab",
    data() {
      return {
        dir:_store.account.direction
      }
    },
    created() {

    },
    methods: {
      getInitPage(){
        // this.$http.get('')
      }
    },

  };

</script>
<style scoped>
.t-center {
  text-align: center;
}
</style>

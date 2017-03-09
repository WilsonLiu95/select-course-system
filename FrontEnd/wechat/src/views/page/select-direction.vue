<template>
  <div>
    <div class='t-center'>
    <h4>选择专业方向</h4>
   </div>
    <mt-radio title="可选的专业方向列表,请勿频繁重选方向" v-model="finalDirection" :options="canSelectDirOptions">
    </mt-radio>
    <mt-button type="primary" size="large" @click="confirm" class="confirm">
      确认
    </mt-button>
  </div>
</template>
<script>
  export default {
    name: "select-direction",
    data() {
      return {
        canSelectDir: [],
        canSelectDirOptions: [],
        finalDirection: '',
      }
    },
    created() {
      this.getCanSelectDir()
    },
    methods: {
      getCanSelectDir() {
        this.$http.get("account/can-select-dir").then((res) => {
          this.canSelectDir = res.data.data
          this.makeOption(res.data.data)
        })
      },
      makeOption(canSelectDir) {
        var data = [];
        canSelectDir.forEach((item, index) => {
          data.push({
            label: item.name,
            value: String(item.id)
          })
        })
        this.canSelectDirOptions = data
      },
      confirm() {
        util.box.confirm("确定选中该方向？").then(action => {

          // 首先清楚存储的账户数据，以便更新
          _store.account = {}
          // 发送请求
          this.$http.post("account/select-dir", {
            direction_id: this.finalDirection })
        }, action => {
          util.toast("您已取消操作")
        })
      }

    },


  }

</script>
<style scoped>
  .confirm {
    margin-top: 40px;
  }
</style>

<template>
  <div>
    <div class='t-center'>
      <h4>第二步：绑定班级信息</h4>
    </div>
    <mt-radio title="请确认您的班级，选定后不可再更改。" v-model="finalClass" :options="canSelectClassOptions">
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
        canSelectClass: [],
        canSelectClassOptions: [],
        finalClass: '',
      }
    },
    created() {
      this.getCanSelectClass()
    },
    methods: {
      getCanSelectClass() {
        this.$http.get("account/can-select-class").then((res) => {
          this.canSelectClass = res.data
          this.makeOption(res.data)
        })
      },
      makeOption(canSelectClass) {
        var data = [];
        canSelectClass.forEach((item, index) => {
          data.push({
            label: item.name,
            value: String(item.classes_code)
          })
        })
        this.canSelectClassOptions = data
      },
      confirm() {
        util.box.confirm("确定选中该方向？").then(action => {
          // 首先清楚存储的账户数据，以便更新
          _store.account = {}
          // 发送请求
          this.$http.post("account/select-class", { class_code: this.finalClass })
        }, action => {
          util.toast("您已取消操作")
        })
      }

    },


  }

</script>
<style scoped>
  .confirm {
    margin: 20px 0 20px 0;
  }
</style>
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
        this.$http.get("direction/can-select-dir").then((res) => {
          if(res.data.isCanChangeDir){
            this.canSelectDir = res.data.data
            this.finalDirection = String(res.data.hasSelectDir)
            this.makeOption(res.data.data)
          }else{
            util.box.alert(res.data.msg,'提示').then(()=>{
              // 使用replace来控制跳转
              this.$router.replace({name:'direction-course-select'})
              
            })
          }

        })
      },
      makeOption(canSelectDir) {
        var data = [];
        canSelectDir.forEach((item, index) => {
          var option = {
            label: item.name + " 当前:"+item.current_number+ "人",
            value: String(item.id)
          }
          if(item.id == this.finalDirection){
            option.disabled = true
          }
          data.push(option)
        })
        this.canSelectDirOptions = data
      },
      confirm() {
        util.box.confirm("确定选中该方向？").then(action => {
          // 发送请求
          this.$http.post("direction/select-dir", {
            direction_id: Number(this.finalDirection) })
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

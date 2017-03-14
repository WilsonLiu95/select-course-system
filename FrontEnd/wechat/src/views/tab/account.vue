<template>
  <div class="tab-page-container">
    <div class='section-part'>
      <mt-field label="院系" v-model="account.institute" placeholder="电信学院" disableClear readonly></mt-field>
      <!--major由班级确定，特殊班级没有major的概念，如启明-->
      <mt-field v-if="account.major" label="专业" v-model="account.major" placeholder="通信工程" disableClear readonly></mt-field>
      <!--专业方向-->
      <mt-field v-if="account.direction" label="选修方向" v-model="account.direction" placeholder="互联网" disableClear readonly>
        <mt-button size="small" @click="reselectDirection">重选</mt-button>
      </mt-field>
      <mt-field label="班级" v-model="account.classes" placeholder="通信1305班" disableClear readonly></mt-field>
      <mt-field label="姓名" v-model="account.name" placeholder="WilsonLiu" disableClear readonly></mt-field>
      <mt-field label="学号" v-model="account.job_num" placeholder="U19951995" disableClear readonly></mt-field>
    </div>
    <div class='section-part t-center'>
      <div>
        <h4>请确认以上信息，确认后开始选课。</h4>
        <mt-button size='large' type="primary" @click="startSelect">开始选课</mt-button>
      </div>
    </div>
  </div>
</template>
<script>
  export default {
    name: "account-page",
    data() {
      return {
        account: {
          'major': "待选择"
        },
        system_status:[],
        has_select_direction_course:[]
      }
    },
    created() {
        this.getAccount();
    },
    methods: {
      getAccount() {
        this.$http.get("account").then((res) => {
          var data = res.data
          this.account = data.account
          this.system_status = data.system_status
          this.has_select_direction_course = data.has_select_direction_course
        })
      },

      startSelect(){
        if(this.account.direction_id == 0){
          // 没有选取过方向，则先选取方向
          this.$router.push({name:'select-direction'})
        }else if(this.system_status == 1){
          this.$router.push({name:'direction-course'})
        }else if(this.system_status == 2){
          this.$router.push({name:'common-course'})
        }

      },
      reselectDirection(){
          // 首先查看，当前是否仍然有选中的选修课程
          var unQuitCourse = this.has_select_direction_course.length
          var confirmMsg = unQuitCourse ? "当前选修了" + unQuitCourse + "门选修课程。重选方向前，请先退选所有选修课程。" : "确认重选方向？"
          util.box.confirm(confirmMsg).then(action => {
            if(unQuitCourse){
              // 先去退选所有课程
              this.$router.push({name:'direction-course'})
            }else{
              this.$router.push({name:'select-direction'})
            }
          }, action => {
            util.toast("您已取消操作")
          })
          
      }
    },
  }

</script>
<style scoped>
  .mint-cell-wrapper button {
    margin: 7px 0 0 0;
  }
  /*.section-part {
    margin: 0 0 5px 0;
  }*/
</style>

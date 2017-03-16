<template>
  <div class="tab-page-container">
    <div class='firt-part'>
      <mt-field label="院系" v-model="account.institute" placeholder="电信学院" disableClear readonly></mt-field>
      <!--major由班级确定，特殊班级没有major的概念，如启明-->
      <mt-field v-if="account.major" label="专业" v-model="account.major" placeholder="通信工程" disableClear readonly></mt-field>
      <!--专业方向-->
      <mt-field v-if="account.direction_id" label="选修方向" v-model="account.direction" placeholder="互联网" disableClear readonly>
        <mt-button size="small" v-if="[0,1].indexOf(system_status)!=-1  " @click="reselectDirection">重选</mt-button>
      </mt-field>
      <mt-field label="班级" v-model="account.classes" placeholder="通信1305班" disableClear readonly></mt-field>
      <mt-field label="姓名" v-model="account.name" placeholder="WilsonLiu" disableClear readonly></mt-field>
      <mt-field label="学号" v-model="account.job_num" placeholder="U19951995" disableClear readonly></mt-field>
    </div>
    <div class='second-part' v-if="isInit">
      <div v-if="system_status==0">
        <mt-cell title="系统说明" label="系统暂未开放选课，请先预览课程及选择专业方向。" ></mt-cell>
        <mt-button v-if="!account.direction_id" class="second-part-btn" size="large" @click="startSelect(0)" type='primary'>选择专业方向</mt-button>
        <!--<p>系统暂未开放选课，请先预览课程及选择专业方向。</p>-->
      </div>      
      <div v-if="system_status==1">
        <mt-cell title="系统说明" label="系统已开放专业方向选修课，请尽快选课。" ></mt-cell>
    
        <mt-button v-if="has_select_direction_course.length" size='large' type="primary" class="second-part-btn" @click="$router.push({name:'select-result'})">查看选课结果</mt-button>
        <mt-button v-else size='large' type="primary" class="second-part-btn" @click="startSelect(1)">开始选择专业方向课程</mt-button>
      </div>
      <div v-if="system_status==2">
        <mt-cell title="系统说明" label="系统已开放公共选修课，请尽快选课。" ></mt-cell>
        
        <mt-button v-if="has_select_common_course.length" size='large' type="primary" class="second-part-btn" @click="$router.push({name:'select-result'})">查看选课结果</mt-button>
        <mt-button v-else size='large' type="primary" @click="startSelect(2)">开始选择公共选修课程</mt-button>
      </div>
      <div v-if="system_status > 2">
        <mt-cell title="系统说明" label="系统已关闭。" ></mt-cell>
        <mt-button v-if="has_select_common_course.length" size='large' type="primary" class="second-part-btn" @click="$router.push({name:'select-result'})">查看选课结果</mt-button>
        
      </div>
    </div>
  </div>
</template>
<script>
  export default {
    name: "account-page",
    data() {
      return {
        isInit: false,
        account: {
          'major': "待选择"
        },
        system_status:0,
        has_select_direction_course:[],
        has_select_common_course:[],
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
          this.has_select_common_course = data.has_select_common_course
          this.isInit = true
        })
      },

      startSelect(system_status){
        if(system_status == 0 || this.account.direction_id==0){
          // 系统未开放，只能选择专业方向。或者系统开放了，但是用户尚未选择专业方向
          return this.$router.push({name:'select-direction'})
        }
        if(system_status == 1){
          return this.$router.push({name:'direction-course-select'})
        }else if(system_status == 2){
          return this.$router.push({name:'common-course-select'})
        }

      },
      reselectDirection(){
          // 首先查看，当前是否仍然有选中的选修课程
          var unQuitCourse = this.has_select_direction_course.length
          var confirmMsg = unQuitCourse ? "当前选修了" + unQuitCourse + "门选修课程。重选方向前，请先退选所有选修课程。" : "确认重选方向？"
          util.box.confirm(confirmMsg).then(action => {
            if(unQuitCourse){
              // 先去退选所有课程
              this.$router.push({name:'direction-course-quit'})
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
  .second-part {
    margin: 15px 0 0 0;
  }
  .second-part-btn{
    margin: 10px 0 0 0;
  }
</style>

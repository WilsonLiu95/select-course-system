<template>
  <div class="course-select-page">
    <div class='t-center'>
      <h4><span v-if="isQuit">退选></span>专业方向选修课</h4>
    </div>
    <mt-checklist id="" v-if="canSelectCourseOptions.length" title="专业方向课程列表" v-model="finalCourseArr" :options="canSelectCourseOptions"></mt-checklist>
    <div class='confirm-btn-group' @click="confirm">
      <mt-button v-if="isQuit"  type="danger" size="large">退选</mt-button>
      <mt-button v-else  type="primary" size="large" >选课</mt-button>      
    </div>
  </div>
</template>
<script>
  export default {
    name: "select-course",
    data() {
      return {
        isQuit: false,
        canSelectCourse: [],
        canSelectCourseOptions: [],
        finalCourseArr: [],
      }
    },
    created() {
      this.isQuit = (this.$route.params[0] == 'quit') // 判断是否为退选页面
      this.getCanSelectCourse();
    },
    methods: {
      initPage(data){ // 根据 data 初始化页面数据
          this.canSelectCourse = data.courseList
          this.finalCourseArr = data.has_select_course
          this.canSelectCourseOptions = window.util.makeCourseOption(data.courseList, data.has_select_course, this.isQuit)
      },
      getCanSelectCourse() {
        this.$http.get("direction-course/can-select-course").then((res) => {
          this.initPage(res.data);
        })
      },
      getSelectResult(){
        setTimeout(()=>{
          this.$http.get('direction-course/select-result',{ noIndicator:true }).then(res=>{
            if(res.data.isFinish === false){
              return this.getSelectResult() // 没有结束处理，继续请求
            } 
            // 请求成功，结束loading动画
            this.$indicator.close()
            this.initPage(res.data.data) // 根据当下数据重新渲染页面

            // 弹出提示
            var msg = '操作成功'
            for (var key in res.data){
              if(res.data.result[key] == false){   // 只有有课程失败就弹出提示
                msg = '有部分课程，操作失败，请再次尝试'
              }}
            util.box.alert(msg)
          })
        },1000) 
      },
      confirm() {
        var msg = this.isQuit? "退选":"选中"
        util.box.confirm("确定"+ msg + "勾选的课程").then(action => {
          this.$indicator.open({ // 先弹起loading动画
            text: '紧急'+ msg +'ing~',
            spinnerType: 'double-bounce'
          })
          this.$http.post("direction-course/handle-course", {
            course_id_arr: this.finalCourseArr, 
            isQuit:this.isQuit // 告诉后台，这次是选课还是退选课程
          }, {noIndicator: true})// 配置项，取消默认ajax请求的loading动画
          .then(()=>{
            setTimeout(()=>{ // 首次发起请求结果
              this.getSelectResult() // 查询结果
            },1000)
            
          }) 
        }, action => {
          util.toast("您已取消操作")
        })
      }

    },


  }

</script>


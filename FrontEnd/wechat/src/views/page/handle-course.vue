<template>
  <div class="course-page">
    <div  class='t-center'>
        <h4 v-if='isCommonCourse'><span v-if="isQuit">退选</span>公共选修课程</h4>
        <h4 v-else><span v-if="isQuit">退选</span>专业方向选修课</h4>
    </div>
    <mt-checklist id="" v-if="canSelectCourseOptions.length" 
        title="课程列表" v-model="finalCourseArr" :options="canSelectCourseOptions">
    </mt-checklist>
    <div class='btn-group' @click="confirm">
      <mt-button v-if="isQuit"  type="danger" size="large" :disabled='isDisabledSubmit'>退选</mt-button>
      <mt-button v-else  type="primary" size="large" :disabled='isDisabledSubmit'>选课</mt-button>      
    </div>

  </div>
</template>
<script>
  export default {
    name: "course",
    data() {
      return {
        isQuit: false,
        isCommonCourse:false, 
        isAbleHandle: true, // 是否允许操作
        canSelectCourse: [],
        canSelectCourseOptions: [],
        finalCourseArr: [],
        has_select_course: [],
      }
    },
    created() {
      this.isCommonCourse = (this.$route.params[0] == 'common') // 判断是否为退选页面
      this.isQuit = (this.$route.params[1] == 'quit') 
      this.getCanSelectCourse();
    },
    computed:{
      isDisabledSubmit:function(){
        if(this.isQuit){ // 退选，则不可以退选0门课程
          return this.finalCourseArr.length == 0
        }else{ // 选课，则需要多选几门课程
          return this.has_select_course == this.finalCourseArr;
        }
      }
    },
    methods: {
      initPage(data){
          this.canSelectCourse = data.courseList
          this.finalCourseArr = data.has_select_course
          this.has_select_course = data.has_select_course
          this.canSelectCourseOptions = this.makeCourseOption(data.courseList, data.has_select_course, this.isQuit)
      },
      getCanSelectCourse() {
        this.$http.get("handle-course/can-select-course?is_common="+this.isCommonCourse).then((res) => {
          this.initPage(res.data);
        })
      },
      getSelectResult(){
        setTimeout(()=>{
          this.$http.get('handle-course/select-result',{ 
            noIndicator:true, 
            params: {
              is_common: this.isCommonCourse
            }
        }).then(res=>{
          debugger
            if(res.data.isFinish === false){
              return this.getSelectResult() // 没有结束处理，继续请求
            } 
            // 请求成功，结束loading动画
            this.$indicator.close()
            this.isAbleHandle = true // 重新允许操作
            this.initPage(res.data.data) // 根据当下数据重新渲染页面

            // 弹出提示
            var msg = '操作成功'
            for (var key in res.data){
              if(res.data.result[key] == false){   // 只有有课程失败就弹出提示
                msg = '有部分课程，操作失败，请再次尝试'
              }}
            util.box.alert(msg)
          },err=>{
            debugger
          })
        },1000) 
      },
      confirm() {
        if(!this.isAbleHandle){
          return // 不允许重复操作
        }
        var msg = this.isQuit? "退选":"选中"
        util.box.confirm("确定"+ msg + "勾选的课程").then(action => {
          this.isAbleHandle = false// 禁止之后再进行操作
          this.$indicator.open({ // 先弹起loading动画
            text: '紧急'+ msg +'ing~',
            spinnerType: 'double-bounce'
          })
          this.$http.post("handle-course/handle-course", {
            course_id_arr: this.finalCourseArr, 
            is_common: this.isCommonCourse,
            isQuit:this.isQuit // 告诉后台，这次是选课还是退选课程
          }, {noIndicator: true})// 配置项，取消默认ajax请求的loading动画
          .then((res)=>{
            setTimeout(()=>{ // 首次发起请求结果
              this.getSelectResult() // 查询结果
            },500)
          },(err)=>{
            // 失败 
            this.getCanSelectCourse()
            this.$indicator.close()  
          }) 
        }, action => {
          util.toast("您已取消操作")
        })
      },
      makeCourseOption(canSelectCourse, finalCourseArr, isQuit) {
        var data = [];
        canSelectCourse.forEach((item, index) => {
          var option = {
            label: " 学分:" + item.credit + " 人数" + item.current_number + "/" + item.required_number + " " + "《" + item.title + "》",
            value: item.id
          }
          if (finalCourseArr.indexOf(item.id) == -1) {
            option.disabled = isQuit ? true : false // 推选课程，则需要已经选过课程
          } else {
            option.disabled = isQuit ? false : true
          }
          data.push(option)
        })
        return data
      },

    },


  }

</script>

<style>
.course-page  .mint-checkbox-label{
  font-size: 12px;
}
</style>
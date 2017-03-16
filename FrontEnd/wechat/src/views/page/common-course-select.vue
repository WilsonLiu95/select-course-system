<template>
  <div>
    <div class='t-center'>
      <h4>公共选修课程</h4>
    </div>

    <mt-checklist id="course-check-item" v-if="canSelectCourseOptions.length" :max="7" title="公共选修课程列表" v-model="finalCourseArr" :options="canSelectCourseOptions"></mt-checklist>
    <mt-button type="primary" size="large" @click="confirm" class="confirm">
      确认
    </mt-button>
  </div>
</template>
<script>
  export default {
    name: "common-course-select",
    data() {
      return {
        canSelectCourse: [],
        canSelectCourseOptions: [],
        finalCourseArr: [],
      }
    },
    created() {
      this.getCanSelectCourse();
    },
    methods: {
      getCanSelectCourse() {
        this.$http.get("common-course/can-select-course").then((res) => {
          this.canSelectCourse = res.data.courseList
          this.finalCourseArr = res.data.has_select_common_course
          this.makeOption(res.data.courseList)
        })
      },
      getSelectResult(){
        setTimeout(()=>{
          this.$http.get('common-course/select-result',{ noIndicator:true }).then(res=>{
            if(res.data.isFinish === false){
              this.getSelectResult() // 没有结束处理，继续请求
            } else {
              this.$indicator.close()
              var msg = '选课成功'
              for (var key in res.data){
                if(res.data[key] == false){   // 只有有课程失败就弹出提示
                  msg = '有部分课程，选课失败，请再次尝试'
                }}
              util.box.alert(msg).then(type=>{
                this.getCanSelectCourse()
              })
            }
          })
        },1500) // 每1.5S发送一次请求
      },
      makeOption(canSelectCourse) {
        var data = [];
        canSelectCourse.forEach((item, index) => {
          var option = {
            label: " 学分:" + item.credit + " 人数" + item.current_number + "/" + item.required_number + " " + "《"+item.title +"》",
            value: item.id
          }
          if(this.finalCourseArr.indexOf(item.id) != -1){
            option.disabled = true
          }
          data.push(option)
        })
        this.canSelectCourseOptions = data
      },
      confirm() {
        util.box.confirm("确定选中该方向？").then(action => {
          this.$http.post("common-course/handle-course", { course_id_arr: this.finalCourseArr, isQuit:false},{noIndicator: true}) 
          this.$indicator.open({
            text: '紧急抢课ing~',
            spinnerType: 'double-bounce'
          })
          this.getSelectResult()
        }, action => {
          util.toast("您已取消操作")
        })
      }

    },


  }

</script>
<style>
  .confirm {
    margin: 20px 0 20px 0;
  }
  #course-check-item .mint-checkbox-label{
    font-size: 12px;
  }
</style>
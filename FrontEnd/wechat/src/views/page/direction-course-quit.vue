<template>
  <div>
    <div class='t-center'>
      <h4>退选专业方向选修课</h4>
    </div>
    <mt-checklist id="course-check-item" v-if="canSelectCourseOptions.length" title="专业方向课程列表" v-model="finalCourseArr" :options="canSelectCourseOptions"></mt-checklist>
    <mt-button type="danger" size="large" @click="confirm" class="confirm">
      退选
    </mt-button>
  </div>
</template>
<script>
  export default {
    name: "select-course-quit",
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
        this.$http.get("direction-course/can-select-course").then((res) => {
          this.canSelectCourse = res.data.courseList
          this.finalCourseArr = res.data.has_select_direction_course
          this.makeOption(res.data.courseList)
        })
      },
      getSelectResult(){
        setTimeout(()=>{
          this.$http.get('direction-course/select-result',{ noIndicator:true }).then(res=>{
            if(res.data.isFinish === false){
              this.getSelectResult() // 没有结束处理，继续请求
            } else {
              this.$indicator.close()
              console.log(res)
            }
          })
        },1000) // 每1S发送一次请求
      },
      makeOption(canSelectCourse) {
        var data = [];
        canSelectCourse.forEach((item, index) => {
          var option = {
            label: " 学分:" + item.credit + " 人数" + item.current_number + "/" + item.required_number + " " + "《"+item.title +"》",
            value: item.id
          }
          if(this.finalCourseArr.indexOf(item.id) == -1){
            option.disabled = true
          }
          data.push(option)
        })
        this.canSelectCourseOptions = data
      },
      confirm() {
        util.box.confirm("确定选中该方向？").then(action => {
          this.$http.post("direction-course/handle-course", { course_id_arr: this.finalCourseArr, isQuit:true},{noIndicator: true}) 
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
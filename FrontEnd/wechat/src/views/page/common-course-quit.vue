<template>
  <div>
    <div class='t-center'>
      <h4>公选课</h4>
    </div>

    <mt-checklist id="course-check-item" v-if="canSelectCourseOptions.length" :max="7" title="专业方向课程列表" v-model="finalCourseArr" :options="canSelectCourseOptions"></mt-checklist>
    <mt-button type="primary" size="large" @click="confirm" class="confirm">
      确认
    </mt-button>
  </div>
</template>
<script>
  export default {
    name: "common-course",
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
          data.push({
            label: " 学分:" + item.credit + " 人数" + item.current_number + "/" + item.required_number + " " + "《"+item.title +"》",
            value: item.id
          })
        })
        this.canSelectCourseOptions = data
      },
      confirm() {
        util.box.confirm("确定选中该方向？").then(action => {
          this.$http.post("direction-course/handle-course", { course_id_arr: this.finalCourseArr, isQuit:false},{noIndicator: true}) 
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
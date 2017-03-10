<template>
  <div>
    <div class='t-center'>
      <h4>选定专业方向选修课</h4>
    </div>

    <mt-checklist v-if="canSelectCourseOptions.length" :max="7" title="专业方向课程列表" v-model="finalCourseArr" :options="canSelectCourseOptions"></mt-checklist>
    <mt-button type="primary" size="large" @click="confirm" class="confirm">
      确认
    </mt-button>
  </div>
</template>
<script>
  export default {
    name: "select-course",
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
        this.$http.get("select-course/can-select-course").then((res) => {
          this.canSelectCourse = res.data.courseList
          this.makeOption(res.data.courseList)
        })
      },
      makeOption(canSelectCourse) {
        var data = [];
        canSelectCourse.forEach((item, index) => {
          data.push({
            label: item.title,
            value: String(item.id)
          })
        })
        this.canSelectCourseOptions = data
      },
      confirm() {
        util.box.confirm("确定选中该方向？").then(action => {
          this.$http.post("select-course/select-course", { course_id_arr: Number(this.finalCourseArr) })

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
<template>
  <div class="details-page">
    <!--第一部分 start 基本课程信息-->
    <div class="detail-section">
      <mt-field label="课程" placeholder="课程名称" v-model="course.title" disableClear	 readonly></mt-field>
      <mt-field label="导师" placeholder="导师姓名" v-model="course.teacher" disableClear readonly></mt-field>
      <mt-field label="人数" placeholder="37" v-model="course.required_number" disableClear readonly></mt-field>
      <mt-field label="学分" placeholder="3" v-model="course.credit" disableClear readonly></mt-field>

      <mt-field v-if="course.detail" label="详情" placeholder="课题详情" type="textarea" rows="8" v-model="course.detail" disableClear readonly></mt-field>
    </div>

    <!--第一部分 end 基本课程信息-->
  </div>
</template>
<script>
  export default {
    name: "details",
    data() {
      return {
        course: {}
      }
    },
    created() {
      this.getDetail();
    },
    methods: {
      getDetail() {
        if(window._store.hasStoreCourse){
            // 如果有存储，则直接用本地的课程数据
            var course = window._store.course
            this.course = course[this.$route.params.direction_index]['course'][this.$route.params.course_index]
        }else{
          this.$http.get("/course").then((res) => {
            // 存储在全局中，挡掉之后的请求
            _store.course = res.data
            _store.hasStoreCourse = true
            this.course = res.data[this.$route.params.direction_index]['course'][this.$route.params.course_index]
        })
        }
      },
    },

  };

</script>
<style scoped>
</style>
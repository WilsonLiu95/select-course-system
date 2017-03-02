<template>
  <div class="details-page">
    <!--第一部分 start 基本课程信息-->
    <div class="detail-section">
      <mt-field label="课程" placeholder="课程名称" v-model="course.title" :disabled="!course.isowner || isDiabled"></mt-field>
      <mt-field label="导师" placeholder="导师姓名" v-model="course.teacher" disabled></mt-field>
      <mt-field label="电话" placeholder="导师电话" v-model="course.teacher_phone" disabled></mt-field>
      <mt-field label="详情" placeholder="课题详情" type="textarea" rows="8" v-model="course.details" :disabled="!course.isowner || isDiabled"></mt-field>
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
        // 请求数据
        this.$http.get("detail?id=" + this.$route.params.courseId).then((res) => {
          this.course = res.data.data
        })
      },
    },

  };

</script>
<style scoped>

</style>

<template>
  <div class="details-page">
    <!--第一部分 start 基本课程信息-->
    <div class="detail-section">
      <mt-field label="课程" placeholder="课程名称" v-model="course.title" disabled></mt-field>
      <mt-field label="导师" placeholder="导师姓名" v-model="course.teacher" disabled></mt-field>
      <mt-field label="人数" placeholder="37" v-model="course.required_number" disabled></mt-field>
      <mt-field label="学分" placeholder="3" v-model="course.credit" disabled></mt-field>

      <mt-field v-if="course.detail" label="详情" placeholder="课题详情" type="textarea" rows="8" v-model="course.detail" disabled></mt-field>
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
        this.$http.get("course/detail?id=" + this.$route.params.course_id).then((res) => {
          this.course = res.data
        })
      },
    },

  };

</script>
<style scoped>
</style>
<template>
  <div class="tab-page-container">
    <el-card class="box-card">
      <div>
        <el-button v-for="grade in grade_list">{{grade.name}}</el-button>
      </div>
    </el-card>
    <el-tabs v-model="current_page"
             type="card"
             @tab-click="handleClick">
      <el-tab-pane label="统计数据"
                   name="record">
  
      </el-tab-pane>
      <el-tab-pane label="学生管理"
                   name="student">
  
      </el-tab-pane>
      <el-tab-pane label="班级管理"
                   name="classes">
  
      </el-tab-pane>
    </el-tabs>
  </div>
</template>
<script>
export default {
  name: "home-page",
  data() {
    return {
      current_page: 'first',
      current_grade: 1,
      grade_list: [],
    }
  },
  created() {
    this.init()

  },
  methods: {
    init(isLoading) {
      this.$http.get('home', { isLoading: isLoading }).then(res => {
        this.grade_list = res.data.grade_list
      })
    },
    handleClick(tab) {

    }
  }
}

</script>
<style>

</style>

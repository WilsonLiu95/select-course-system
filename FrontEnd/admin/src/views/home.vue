<template>
  <div class="tab-page-container">
    <el-tabs v-model="activeDirection">
      <el-tab-pane v-for="dir in direction"
                   :label="dir.name"
                   :name="String(dir.id)">
      </el-tab-pane>
    </el-tabs>
    
    <el-table v-if="direction[activeDirection]" :data="direction[activeDirection].course"
              border
              style="width: 100%"
              class="course-table">
      <el-table-column type="expand">
        <template scope="props">
          <el-form label-position="left"
                   inline
                   class="demo-table-expand">
            <el-form-item label="课程名">
              <span>{{ props.row.title }}</span>
            </el-form-item>
            <el-form-item label="老师">
              <span>{{ props.row.teacher }}</span>
            </el-form-item>
            <el-form-item label="所需人数">
              <span>{{ props.row.required_number }}</span>
            </el-form-item>
            <el-form-item label="学分">
              <span>{{ props.row.credit }}</span>
            </el-form-item>
            <el-form-item label="详情">
              <span>{{ props.row.detail }}</span>
            </el-form-item>
          </el-form>
        </template>
      </el-table-column>
      <el-table-column prop="title"
                       label="课程名">
      </el-table-column>
      <el-table-column prop="teacher"
                       label="老师">
      </el-table-column>
      <el-table-column prop="required_number"
                       label="人数">
      </el-table-column>
      <el-table-column prop="credit"
                       label="学分">
      </el-table-column>
  
      <el-table-column label="操作">
        <template scope="scope">
  
          <el-button size="small"
                     type="primary"
                     @click="handleEdit(scope.$index, scope.row)">修改</el-button>
          <el-button size="small"
                     type="danger"
                     @click="handleDelete(scope.$index, scope.row)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>
  
  </div>
</template>
<script>

export default {
  name: "home-page",
  data() {
    return {
      direction: [],
      activeDirection: 0,
      currentPage4: 4
    }
  },
  created() {
    this.getCourse()
  },
  methods: {
    getCourse() {
      this.$http.get('home/course').then(res => {
        // debugger
        this.direction = res.data
      })
    },
    handleEdit(index, row) {
      console.log(index, row);
    },
    handleDelete(index, row) {
      console.log(index, row);
    },
    filterTag(value, row) {
      // return row.status === value;
    },
    handleSizeChange(val) {
      console.log(`每页 ${val} 条`);
    },
    handleCurrentChange(val) {
      this.currentPage = val;
      console.log(`当前页: ${val}`);
    }
  }
}

</script>
<style>
.record_form {
  margin: 10px 0;
}
</style>

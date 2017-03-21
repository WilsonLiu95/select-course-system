<template>
  <div class="tab-page-container">
  
    <!--=========================================================学生管理 start===================================================================-->
  
    <el-card class="box-card">
      <div slot="header"
           class="clearfix">
  
        <el-input placeholder="搜索学生(支持姓名与学号搜索)"
                  icon="search"
                  v-model="search"
                  style="width:300px;"
                  :on-icon-click="handleSearchClick">
        </el-input>
        <el-button @click="addOne('student')"
                   type="primary">新增学生</el-button>
        <el-upload class="upload-demo"
                   ref="upload"
                   action="//jsonplaceholder.typicode.com/posts/"
                   style="width:300px;float: right;"
                   :auto-upload="false">
          <el-button slot="trigger"
                     size="small"
                     type="primary">选择文件</el-button>
          <el-button style="margin-left: 10px;"
                     size="small"
                     type="success"
                     @click="submitUpload">确认上传</el-button>
          <div slot="tip"
               class="el-upload__tip">上传学生的EXCEL，用于导入学生数据</div>
        </el-upload>
      </div>
      <el-table :data="student_list.data"
                border
                @selection-change="handleSelectionChange"
                style="width: 100%">
        <el-table-column type="selection"
                         width="55">
        </el-table-column>
        <el-table-column prop="id"
                         label="id"
                         width="180">
        </el-table-column>
        <el-table-column prop="name"
                         label="姓名"
                         width="180">
        </el-table-column>
        <el-table-column prop="job_num"
                         label="学号"
                         width="180">
        </el-table-column>
        <el-table-column prop="openid"
                         label="微信识别id"
                         width="180">
        </el-table-column>
        <el-table-column prop="direction"
                         label="方向"
                         width="180">
        </el-table-column>
        <el-table-column prop="class"
                         label="班级"
                         width="180">
        </el-table-column>
        <el-table-column label="操作">
          <template scope="scope">
  
            <el-button size="small"
                       type="primary"
                       @click="handleEdit(scope.$index, scope.row, 'classes')">修改</el-button>
            <el-button size="small"
                       type="danger"
                       @click="handleDelete(scope.$index, scope.row, 'classes')">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
      <div class="block">
        <el-pagination @current-change="studentPageInit"
                       :current-page="student_list.current_page"
                       :page-size="student_list.per_page"
                       layout="total,sizes,prev, pager, next, jumper"
                       :total="student_list.total">
        </el-pagination>
      </div>
    </el-card>
  
  </div>
</template>
<script>
export default {
  name: "home-page",
  data() {
    return {
      student_list: {},
      search: '',
      newStudent: {
      },

      dialog: false,
      dialogId: 0
    }
  },
  created() {
    this.init({
      page: 1
    })
  },
  methods: {
    init(option, isLoading) {
      // 统一接口
      this.$http.get('student/student-init', {
        params: option,
        isLoading: isLoading
      }).then(res => {
        this.student_list = res.data.student_list
      })
    },
    addOne() {
      this.dialog = true
      this.dialogType = type
      this.dialogId = 0
      this.newStudent = {}
    },
    submitEdit() {
      this.$confirm('确认提交该操作？请仔细核对数据。', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        this.$http.post('student/student-submit', this.newClasses, {
          params: {
            id: this.dialogId,
          }
        }).then(res => {
          this.dialog = false
          this.studentPageInit(true)
        })
      }).catch(() => {
        this.$message({
          type: 'info',
          message: '已取消操作'
        });
      })

    },
    handleEdit(index, item, type) {
      this.dialog = true
      this.dialogId = item.id

      this.newClasses.name = item.name
      this.newClasses.major_id = Number(item.major_id)
      this.newClasses.classes_code = item.classes_code

    },
    handleDelete(index, item, type) {
      this.$confirm('确认删除该条数据？请仔细确认', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        this.$http.get('student/student-delete?id=' + item.id).then(res => {
          this.classesPageInit(true)
        })

      }).catch(() => {
        this.$message({
          message: '已取消操作',
          type: 'info',
        })
      })
    },
    handleSearchClick() {

    },
    submitUpload() {

    },
    handleSelectionChange() {

    }
  }
}

</script>
<style>

</style>

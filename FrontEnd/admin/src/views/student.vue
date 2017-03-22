<template>
  <div class="tab-page-container">
  
    <!--=========================================================学生管理 start===================================================================-->
  
    <el-card class="box-card">
      <div slot="header"
           class="clearfix">
  
        <el-input placeholder="搜索学生(支持姓名与学号搜索)"
                  icon="search"
                  v-model="option.search"
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
                @filter-change="filterTag"
                @sort-change="res=>{option.orderBy.prop = res.prop;option.orderBy.order = res.order}"
                @selection-change="handleSelectionChange"
                style="width: 100%">
        <el-table-column type="selection"
                         width="55">
        </el-table-column>
        <el-table-column prop="id"
                         label="id"
                         sortable
                         width="180">
        </el-table-column>
        <el-table-column prop="name"
                         label="姓名"
                         width="180">
        </el-table-column>
        <el-table-column prop="job_num"
                         label="学号"
                         sortable
                         width="180">
        </el-table-column>
        <el-table-column prop="openid"
                         label="微信识别id"
                         width="180">
        </el-table-column>
        <el-table-column prop="direction"
                         label="方向"
                         column-key="direction"
                         :filters="[{ text: '家', value: '1' }, { text: '公司', value: '2' }]"
      
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
        <el-pagination @current-change="newPage=>{option.page = newPage}"
                       @size-change="newPageSize=>{option.pageSize = newPageSize}"
                       :current-page="option.page"
                       :page-size="option.pageSize"
                       layout="total,sizes,prev, pager, next, jumper"
                       :total="student_list.total">
        </el-pagination>
      </div>
    </el-card>
    <!--=========================================================dialog start===================================================================-->
    <el-dialog title="学生"
               v-model="dialog">
      <!--班级对话框-->
      <el-form :model="newStudent">
        <el-form-item label="学生名">
          <el-input placeholder="请输入学生名"
                    v-model="newStudent.name"></el-input>
        </el-form-item>
        <el-form-item label="学号">
          <el-input placeholder="请输入学号"
                    v-model="newStudent.job_num"></el-input>
        </el-form-item>
  
      </el-form>
      <div slot="footer"
           class="dialog-footer">
        <el-button @click="dialog = false">取 消</el-button>
        <el-button type="primary"
                   @click="submitEdit">确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>
<script>
export default {
  name: "home-page",
  data() {
    return {
      student_list: {},
      option: {
        search: '', // 搜索框内容
        pageSize: 10,
        page: 1,
        orderBy: {
          prop: 'job_num',
          order: 'desc'
        },
        filter: {
          key: '',
          in: []
        },
      },
      multipleSelection: [], // 多选列
      newStudent: {
        name: '刘盛',
        job_num: '201313759'
      },
      dialog: false,
      dialogId: 0
    }
  },
  created() {
    this.init(this.option)
  },
  methods: {
    filterTag(){
      debugger
    },
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
      this.dialogId = 0
      this.newStudent = {
        name: '',
        job_num: ''        
      }
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
      for(var key in item){
        this.newStudent[key] = item[key]
      }

    },
    handleDelete(index, item, type) {
      this.$confirm('确认删除该条数据？请仔细确认', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        this.$http.get('student/student-delete?id=' + item.id).then(res => {
          this.init(this.option, true,)
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
      debugger
    },

  }
}

</script>
<style>

</style>

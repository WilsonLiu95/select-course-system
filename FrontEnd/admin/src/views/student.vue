<template>
  <div class="tab-page-container">
    <!--=========================================================学生管理 start===================================================================-->
    <el-card class="box-card">
      <div slot="header"
           class="clearfix">
        <el-input placeholder="搜索学生(支持姓名与学号搜索)"
                  icon="search"
                  v-model="option.search.rule"
                  style="width:300px;"
                  class="search-input"
                  :on-icon-click="()=>{init(false)}">
        </el-input>
        <el-button @click="addOne('student')"
                   type="primary">新增学生</el-button>
        <el-button @click="deleteStudent(multipleSelection)"
                   type="danger">删除选定学生</el-button>
        <el-upload :action="$http.defaults.baseURL + 'student/file'"
                   name="excel"
                   :on-success='fileUpload'
                   :on-error='err=>{$message({ message: "导入失败", type: "error" })}'
                   :headers="{'X-XSRF-TOKEN':token}"
                   style="width:300px;float: right;">
          <el-button size="small"
                     type="primary">点击上传</el-button>
          <div slot="tip"
               class="el-upload__tip">上传学生的EXCEL，用于导入学生数据</div>
        </el-upload>
      </div>
      <el-table :data="student_list.data"
                border
                @filter-change="filterChange"
                @sort-change="res=>{option.orderBy ={
                                key: res.prop,
                                order:res.order == 'descending' ? 'desc':'asc'
                              } }"
                @selection-change="handleSelectionChange"
                style="width: 100%">
        <el-table-column type="selection"
                         width="55">
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
                         column-key="direction_id"
                         :filters="direction_filters"
                         :formatter="(row,col)=>{return direction_map[row.direction_id]}"
                         width="180">
        </el-table-column>
        <el-table-column prop="class"
                         label="班级"
                         column-key="classes_id"
                         :filters="classes_filters"
                         :formatter="(row,col)=>{return classes_map[row.classes_id]}"
                         width="180">
        </el-table-column>
        <el-table-column label="操作">
          <template scope="scope">
  
            <el-button size="small"
                       type="primary"
                       @click="handleEdit(scope.$index, scope.row, 'classes')">修改</el-button>
            <el-button size="small"
                       type="danger"
                       @click="deleteStudent([scope.row.id])">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
      <div class="block">
        <el-pagination @current-change="newPage=>{option.page = newPage}"
                       @size-change="newPageSize=>{option.size = newPageSize}"
                       :current-page="option.page"
                       :page-size="option.size"
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
        <el-select v-model="newStudent.classes_id"
                   clearable
                   placeholder="请选择班级">
          <el-option v-for="item in classes_filters"
                     :label="item.text"
                     :value="item.value">
          </el-option>
        </el-select>
  
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
      token: window.util.cookie.get('XSRF-TOKEN'),
      student_list: {},
      option: {
        search: {
          key: ['name', 'job_num'],
          rule: ""
        }, // 搜索框内容
        size: 20,
        page: 1,
        orderBy: {
          key: 'id',
          order: 'desc'
        },
        filter: {},
      },
      classes_map: [],
      classes_filters: [],
      direction_mao: [],
      direction_filters: [],
      multipleSelection: [], // 多选列
      newStudent: {
        name: '刘盛',
        job_num: '201313759',
        classes_id: 0,
      },
      dialog: false,
      dialogId: 0
    }
  },
  created() {
    this.init(false)
  },
  mounted() {
    document.querySelector('.search-input input').addEventListener('keypress', (e) => {
      if (e.keyCode === 13) { // 绑定回车事件
        this.init(false)
      }
    })
  },
  computed: {
    isRenewData() { // 声明依赖
      var option = this.option
      var tmp = []
      for (var key in this.option) {
        tmp.push(this.option[key])
      }
      // tmp.push(this.option.search.rule)
      return tmp
    }
  },
  watch: {
    isRenewData() { // 监听相关依赖，如果有变化，则触发更新
      this.init(false)
    }
  },
  methods: {
    fileUpload(res) {      
      this.$message({ message: res.msg, type: res.status? 'success':"error" })
      this.init(true)
    
  },
  filterChange(item) {
    var tmp = {}
    for (var key in this.option.filter) {
      tmp[key] = this.option.filter[key]
    }
    for (var key in item) {
      tmp[key] = item[key]
    }

    this.option.filter = tmp // 重新复制触发更新
  },
  makeFilters(key, data) {
    var tmp = []
    for (var index in data) {
      tmp.push({
        text: data[index],
        value: Number(index)
      })
    }
    this[key] = tmp
  },
  init(noLoading) {
    // 统一接口
    this.$http.get('student/student-init',  {
      noLoading: noLoading,
      params:{
        option: JSON.stringify(this.option)
      }
    }).then(res => {
      this.classes_map = res.data.classes_map
      this.direction_map = res.data.direction_map
      this.makeFilters('classes_filters', this.classes_map)
      this.makeFilters('direction_filters', this.direction_map)
      this.student_list = res.data.student_list
    })
  },
  addOne() {
    this.dialog = true
    this.dialogId = 0
    this.newStudent = {
      name: '',
      job_num: '',
      classes_id: 0
    }
  },
  submitEdit() {
    this.$confirm('确认提交该操作？请仔细核对数据。', '提示', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    }).then(() => {
      this.$http.post('student/student-submit', this.newStudent, {
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
    for (var key in this.newStudent) {
      this.newStudent[key] = item[key]
    }

  },
  deleteStudent(student_list) {
    this.$confirm('确认删除选中的学生？请仔细确认', '提示', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    }).then(() => {
      this.$http.post('student/delete', { student_list: student_list }).then(res => {
        this.init(true)
      })
    }).catch(() => {
      this.$message({
        message: '已取消操作',
        type: 'info',
      })
    })

  },

  handleSelectionChange(list) {
    var tmp = []
    list.forEach(item => {
      tmp.push(item.id)
    })
    this.multipleSelection = tmp
  },
  submitUpload() {

  },
}
}

</script>
<style>

</style>

<template>
  <div class="tab-page-container">
    <!--=========================================================班级管理 start===================================================================-->
  
    <el-card class="box-card">
      <div slot="header"
           class="clearfix">
        <span style="line-height: 36px;">班级管理</span>
        <el-button style="float: right;"
                   @click="addOne()"
                   type="primary">新增班级</el-button>
      </div>
      <el-table :data="classes_list"
                border
                style="width: 100%">
        <el-table-column prop="name"
                         label="班级"
                         width="180">
        </el-table-column>
        <el-table-column label="班级代号"
                         width="180">
  
          <template scope="scope">
            <el-tooltip class="item"
                        effect="dark"
                        content="导入的学生EXCEL表格中，请将班级换成该代号"
                        placement="right">
              <el-button>{{ scope.row.classes_code }}</el-button>
            </el-tooltip>
          </template>
  
        </el-table-column>
        <el-table-column label="专业"
                         width="180">
          <template scope="scope">
            <el-button size="small">{{major_list[scope.row.major_id]}}</el-button>
          </template>
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
    </el-card>
    <!--=========================================================班级管理 end===================================================================-->
    <!--=========================================================dialog start===================================================================-->
    <el-dialog title="课程"
               v-model="dialog">
      <!--班级对话框-->
      <el-form :model="newClasses">
        <el-form-item label="课程名">
          <el-input placeholder="请输入课程名称"
                    v-model="newClasses.name"></el-input>
        </el-form-item>
        <el-form-item label="班级代号">
          <el-input placeholder="请输入班级代号"
                    v-model="newClasses.classes_code"></el-input>
        </el-form-item>
  
        <el-form-item label="专业方向">
          <el-radio-group v-model="newClasses.major_id">
            <el-radio v-for="(major, id) in major_list"
                      :label="Number(id)">{{major}}</el-radio>
          </el-radio-group>
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
      classes_list: [],
      major_list: [],
      newClasses: {
        name: "通信05",
        classes_code: 21,
        major_id: 0,
      },
      dialog: false,
      dialogId: 0,
    }
  },
  created() {
    this.classesPageInit()
  },
  methods: {
    classesPageInit(isLoading) {
      this.$http.get('classes/classes-init', {
        isLoading: isLoading
      }).then(res => {
        this.classes_list = res.data.classes_list
        this.major_list = res.data.major_list
      })
    },
    addOne() {
      this.dialog = true
      this.dialogId = 0

      this.newClasses = {
        name: "",
        classes_code: '',
        major_id: 0,
      }

    },
    submitEdit() {
      this.$confirm('确认提交该操作？请仔细核对数据。', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        this.$http.post('classes/classes-submit', this.newClasses, {
          params: {
            id: this.dialogId,
          }
        }).then(res => {
          this.dialog = false
          this.classesPageInit(true)
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
        this.$http.get('classes/classes-delete?id=' + item.id).then(res => {
          this.classesPageInit(true)
        })

      }).catch(() => {
        this.$message({
          message: '已取消操作',
          type: 'info',
        })
      })
    },
  }
}

</script>
<style>

</style>

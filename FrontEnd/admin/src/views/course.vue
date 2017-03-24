<template>
  <div class="tab-page-container">
    <el-tabs v-model="activeDirection">
      <el-tab-pane v-for="(dir,index) in direction"
                   :label="dir.name"
                   :name="String(index)">
      </el-tab-pane>
    </el-tabs>
    <el-card class="box-card">
      <div slot="header"
           class="clearfix">
        <span style="line-height: 36px;">课程列表</span>
        <el-button style="float: right;"
                   type="primary"
                   @click="handleEdit(true)">添加新的课程</el-button>
      </div>
      <el-table v-if="direction[activeDirection]"
                :data="direction[activeDirection].course"
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
                       @click="handleEdit(false, scope.$index, scope.row)">修改</el-button>
            <el-button size="small"
                       type="danger"
                       @click="handleDelete(scope.$index, scope.row)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>
  
    <!--新增or修改的对话框 start-->
    <el-dialog title="课程"
               v-model="dialog">
      <!--专业方向部分-->
      <el-form :model="newCourse">
        <el-form-item label="课程名">
          <el-input placeholder="请输入课程名称"
                    v-model="newCourse.title"></el-input>
        </el-form-item>
        <el-form-item label="课程码">
          <el-input placeholder="请输入课程码"
                    v-model="newCourse.course_code"></el-input>
        </el-form-item>
        <el-form-item label="学分">
          <el-input placeholder="请输入学分"
                    v-model="newCourse.credit"></el-input>
        </el-form-item>
        <el-form-item label="老师">
          <el-input placeholder="请输入老师名称"
                    v-model="newCourse.teacher"></el-input>
        </el-form-item>
        <el-form-item label="人数">
          <el-input placeholder="请输入课程人数"
                    v-model="newCourse.required_number"></el-input>
        </el-form-item>
        <el-form-item label="详情">
          <el-input placeholder="请输入详情"
                    type="textarea"
                    :rows="6"
                    v-model="newCourse.detail"></el-input>
        </el-form-item>
        <el-form-item label="是否为公选课">
          <el-switch on-text="是"
                     off-text="否"
                     v-model="newCourse.is_common"></el-switch>
        </el-form-item>
        <el-form-item label="专业方向">
          <el-checkbox-group v-model="newCourse.direction">
            <el-checkbox v-for="directionItem in direction"
                         v-if='directionItem.id'
                         :label="directionItem.id">{{directionItem.name}}</el-checkbox>
          </el-checkbox-group>
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
      direction: [],
      activeDirection: 0,
      dialog: false,
      course_id: 0,
      newCourse: {
        direction: [],
        is_common: false,
      }
    }
  },
  created() {
    this.init()
  },
  methods: {
    init(isLoading) {
      this.$http.get('course/course', { isLoading: isLoading }).then(res => {
        this.direction = res.data
      })
    },
    submitEdit() {
      this.$confirm('确认提交本门课程？请仔细确认', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        this.$http.post('course/edit?id=' + this.course_id, this.newCourse).then(res => {
          this.dialog = false
          this.init(false)
        })
      })
        .catch(() => {
          this.$message({
            type: 'info',
            message: '已取消操作'
          });
        })
    },
    handleEdit(isAdd, index, row) {
      this.dialog = true
      var newCourse = this.direction[this.activeDirection].course[index]
      if (isAdd) { // 新增课程
        this.course_id = 0
        this.newCourse = {
          direction: [],
          is_common: false
        }
      } else {
        // debugger
        this.course_id = newCourse.id
        for (var key in newCourse) {
          if (key === 'direction') {
            newCourse.direction.forEach((item, index) => {
              this.newCourse.direction.push(item.id)
            })
          } else if (key === 'is_common') {
            this.newCourse.is_common = Boolean(newCourse[key])
          } else {
            this.newCourse[key] = newCourse[key]
          }
        }

      }
    },
    handleDelete(index, row) {
      this.$confirm('确认删除该门课程？请仔细确认', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        this.$http.get('course/delete?id=' + row.id).then(res => {
          this.init(true)
        })
      })
        .catch(() => {
          this.$message({
            type: 'info',
            message: '已取消操作'
          });
        })
    },

  }
}

</script>
<style>
.record_form {
  margin: 10px 0;
}
</style>

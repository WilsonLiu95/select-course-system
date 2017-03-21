<template>
  <div class="tab-page-container">
    <el-card class="box-card">
      <div slot="header"
           class="clearfix">
        <span style="line-height: 36px;">专业</span>
        <el-button style="float: right;"
                   type="primary"
                   @click="addOne('major')">添加新的专业</el-button>
      </div>
      <el-table :data="major"
                border
                style="width: 100%">
        <el-table-column prop="name"
                         label="专业名称">
        </el-table-column>
  
        <el-table-column label="操作">
          <template scope="scope">
            <el-button size="small"
                       @click="handleEdit(scope.$index, scope.row, 'major')">编辑</el-button>
            <el-button size="small"
                       type="danger"
                       @click="handleDelete(scope.$index, scope.row, 'major')">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>
  
    <el-card class="box-card">
      <div slot="header"
           class="clearfix">
        <span style="line-height: 36px;">专业方向</span>
        <el-button style="float: right;"
                   type="primary"
                   @click="addOne('direction')">添加新的专业方向</el-button>
      </div>
      <el-table :data="direction"
                border
                style="width: 100%">
        <el-table-column prop="name"
                         label="方向名称">
        </el-table-column>
        <el-table-column label="隶属专业">
          <template scope="scope">
            <el-button v-for="major in scope.row.major">{{ major.name }}</el-button>
          </template>
        </el-table-column>
        <el-table-column label="操作">
          <template scope="scope">
            <el-button size="small"
                       @click="handleEdit(scope.$index, scope.row , 'direction')">编辑</el-button>
            <el-button size="small"
                       type="danger"
                       @click="handleDelete(scope.$index, scope.row, 'direction')">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>
    <el-card class="box-card">
      <div slot="header"
           class="clearfix">
        <span style="line-height: 36px;">系统设置</span>
      </div>
      <el-form ref="config"
               :model="config"
               label-width="180px">
        <el-form-item label="公选课-最低学分">
          <el-input v-model="config.min_common_credit"></el-input>
        </el-form-item>
        <el-form-item label="公选课-最高学分">
          <el-input v-model="config.max_common_credit"></el-input>
        </el-form-item>
  
        <el-form-item label="公选课">
          <el-switch on-text="开放"
                     off-text="关闭"
                     v-model="config.is_common_open"></el-switch>
        </el-form-item>
  
        <el-form-item label="专业方向选修课-最低学分">
          <el-input v-model="config.min_direction_credit"></el-input>
        </el-form-item>
        <el-form-item label="专业方向选修课-最高学分">
          <el-input v-model="config.max_direction_credit"></el-input>
        </el-form-item>
  
        <el-form-item label="专业方向选修课">
          <el-switch on-text="开放"
                     off-text="关闭"
                     v-model="config.is_direction_open"></el-switch>
        </el-form-item>
  
        <el-form-item>
          <el-button type="primary"
                     @click="systemConfigEdit">确认</el-button>
        </el-form-item>
      </el-form>
    </el-card>
    <!--新增or修改的对话框 start-->
    <el-dialog :title="dialogType==='major' ? '专业':'专业方向'"
               v-model="dialog">
      <!--专业部分-->
      <el-form :model="newMajor"
               v-if="dialogType==='major'">
        <el-form-item label="专业名称">
          <el-input v-model="newMajor.name"></el-input>
        </el-form-item>
      </el-form>
      <!--专业方向部分-->
      <el-form :model="newDirection"
               v-if="dialogType==='direction'">
        <el-form-item label="专业方向名称">
          <el-input v-model="newDirection.name"></el-input>
        </el-form-item>
        <el-form-item label="专业方向名称">
          <el-checkbox-group v-model="newDirection.major">
            <el-checkbox v-for="majorItem in major"
                         :label="majorItem.id">{{majorItem.name}}</el-checkbox>
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
  name: "info-page",
  data() {
    return {
      major: [],
      newMajor: {
        name: 'test'
      },
      direction: [],
      newDirection: {
        name: "互联网",
        major: [], // 属于哪几个专业
      },
      config: {},
      dialog: false,
      dialogType: 'major',
      editId: 0,
      editIndex: 0,

    }
  },
  created() {
    this.init()
  },
  methods: {
    init(isLoading) {
      this.$http.get('info', { noLoading: isLoading }).then(res => {
        this.major = res.data.major
        this.direction = res.data.direction
        this.config = res.data.config
        this.config.is_common_open = Boolean(this.config.is_common_open)
        this.config.is_direction_open = Boolean(this.config.is_direction_open)
      })
    },
    addOne(type) {
      this.dialog = true // 显示弹窗
      this.dialogType = type
      this.editId = 0
      if (type === 'major') {
        this.newMajor = {}
      } else if (type === 'direction') {
        this.newDirection = {
          name: "",
          major: [], // 属于哪几个专业
        }
      }
    },
    handleEdit(index, item, type) {
      this.dialog = true // 显示弹窗
      this.dialogType = type
      this.editIndex = index
      this.editId = item.id

      if (type === 'major') {
        this.newMajor = this.major[index]
      } else if (type === 'direction') {
        this.newDirection.name = this.direction[index].name
        this.newDirection.major = this.direction[index].major.map((item, index) => {
          return item.id // 构造符合条件的major.id数组
        })
      }
    },
    handleDelete(index, item, type) {
      // debugger
      this.$confirm('确认删除该条数据？请仔细确认', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
          this.$http.get('info/' + type + '-delete?id=' + item.id).then(res => {
            this.init(true)
          })
      }).catch(() => {
        this.$message({
          type: 'info',
          message: '已取消操作'
        });
      })
    },
    submitEdit() {
      this.$confirm('确认提交该操作？请仔细核对数据。', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        if (this.dialogType === 'major') {
          this.$http.post('info/major-update?id=' + this.editId, this.newMajor).then(res => {
            this.dialog = false
            this.init(true)
          })
        } else if (this.dialogType === 'direction') {
          this.$http.post('info/direction-update?id=' + this.editId, this.newDirection).then(res => {
            this.dialog = false
            this.init(true)
          })
        }
      }).catch(() => {
        this.$message({
          type: 'info',
          message: '已取消操作'
        });
      })

    },
    systemConfigEdit() {
      this.$http.post('info/edit', this.config)
    },
  }
}

</script>
<style>

</style>

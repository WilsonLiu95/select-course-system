<template>
  <div class="tab-page-container">
    <el-card class="box-card">
      <div slot="header"
           class="clearfix">
        <span style="line-height: 36px;">系统设置</span>
        <el-button type="primary"
                   style="float:right"
                   @click="systemConfigEdit">提交修改</el-button>
      </div>
      <el-form ref="config"
               :model="config"
               :inline="true"
               label-width="180px">
  
        <el-form-item label="选修课|最低学分">
          <el-input-number v-model="config.min_direction_credit"></el-input-number>
        </el-form-item>
        <el-form-item label="选修课|最高学分">
          <el-input-number v-model="config.max_direction_credit"></el-input-number>
        </el-form-item>
        <el-form-item label="是否开放">
          <el-switch on-text="开放"
                     off-text="关闭"
                     v-model="config.is_direction_open"></el-switch>
        </el-form-item>
      </el-form>
      <el-form ref="config"
               :model="config"
               :inline="true"
               label-width="180px">
        <el-form-item label="公选课|最低学分">
          <el-input-number v-model="config.min_common_credit"></el-input-number>
        </el-form-item>
        <el-form-item label="公选课|最高学分">
          <el-input-number v-model="config.max_common_credit"></el-input-number>
        </el-form-item>
        <el-form-item label="是否开放">
          <el-switch on-text="开放"
                     off-text="关闭"
                     v-model="config.is_common_open"></el-switch>
        </el-form-item>
      </el-form>
  
    </el-card>
  
    <!--=========================================================选课记录 start===================================================================-->
    <el-card class="box-card">
      <div slot="header"
           class="clearfix">
  
        <el-input placeholder="搜索课程(支持课程名称，课程编号，课程老师搜索)"
                  icon="search"
                  class="search-input"
                  v-model="option.search.rule"
                  style="width:400px;"
                  :on-icon-click="()=>{getCourse(option, false)}">
        </el-input>
        <el-button @click="addOneGrade()"
                   v-if="now_grade.id==0"
                   type="primary">新建学年</el-button>
        <el-button @click="deleteCurrentGrade"
                   v-if="now_grade.id"
                   type="danger">结束学年</el-button>
      </div>
      <el-table :data="course.data"
                stripe
                border
                @sort-change="res=>{option.orderBy ={
                                                key: res.prop,
                                                order:res.order == 'descending' ? 'desc':'asc'
                                              } }"
                style="width: 100%">
        <!--展开部分-->
        <el-table-column type="expand">
          <template scope="props">
            <span>学生名单： </span>
            <el-button v-for="item in props.row.name_list">{{item}}</el-button>
          </template>
        </el-table-column>
        <el-table-column prop="title"
                         label="课程名">
        </el-table-column>
        <el-table-column prop="course_code"
                         label="课程编码">
        </el-table-column>
        <el-table-column prop="teacher"
                         label="老师">
        </el-table-column>
        <el-table-column prop="credit"
                         label="学分">
        </el-table-column>
        <el-table-column prop="required_number"
                         label="课堂容量">
        </el-table-column>
        <el-table-column prop="current_num"
                         sortable
                         label="选中人数">
        </el-table-column>
  
      </el-table>
      <div class="block">
        <el-pagination @current-change="newPage=>{option.page = newPage}"
                       @size-change="newPageSize=>{option.size = newPageSize}"
                       :current-page="option.page"
                       :page-size="option.size"
                       layout="total,sizes,prev, pager, next, jumper"
                       :total="course.total">
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
      config: {},
      course: {},
      now_grade: {},
      option: {
        search: {
          key: ['teacher', 'title', 'course_code'],
          rule: ""
        }, // 搜索框内容
        size: 20,
        page: 1,
        orderBy: {
          key: 'current_num',
          order: 'desc'
        },
        filter: {},
      },
    }
  },
  created() {
    this.getConfig()
    this.getCourse()
  },
  mounted() {
    document.querySelector('.search-input input').addEventListener('keypress', (e) => {
      if (e.keyCode === 13) { // 绑定回车事件
        this.getCourse(false)
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
      return tmp
    }
  },
  watch: {
    isRenewData() { // 监听相关依赖，如果有变化，则触发更新
      this.getCourse(false)
    }
  },
  methods: {
    getConfig() {
      this.$http.get('home/config').then(res => {
        this.config = res.data.config
        this.config.is_common_open = Boolean(this.config.is_common_open)
        this.config.is_direction_open = Boolean(this.config.is_direction_open)
      })
    },
    getCourse(noLoading) {
      this.$http.get('home/course', {
        noLoading: noLoading,
        params: {
          option: JSON.stringify(this.option)
        },
      }).then(res => {
        if (!res.data.now_grade) { // 未创建学年
          this.now_grade.id = 0
        } else {
          this.now_grade = res.data.now_grade
          this.course = res.data.course
        }

      })
    },
    systemConfigEdit() {
      if (this.config.min_common_credit > this.config.max_common_credit || this.config.min_direction_credit > this.config.maxs_direction_credit) {
        this.$message({
          message: '最低学分不允许高于最高学分',
          type: 'error'
        })
        return false
      } else {
        this.$confirm('确认提交本次系统设置的修改？', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          this.$http.post('home/edit', this.config)
        }).catch(() => {
          this.$message({
            type: 'info',
            message: '已取消操作'
          });
        })
      }
    },
    deleteCurrentGrade() {
      if (!this.now_grade.id) {
        return false
      }
      this.$confirm('确认结束本学年，请确保已导出excel。如非必须请保持关闭选修课与公选课选课状态，直至下一学年。', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        this.$http.post('home/delete').then(res => {
          this.getCourse(true)
        })
      }).catch(() => {
        this.$message({
          type: 'info',
          message: '已取消操作'
        });
      })
    },
    addOneGrade() {
      this.$prompt('请输入学年名称', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
      }).then(({ value }) => {
        this.$http.post('home/add-new-year', { grade_name: value })
      }).catch(() => {
        this.$message({
          type: 'info',
          message: '取消输入'
        });
      });
    }
  }

}

</script>
<style>
.box-card {
  margin-bottom: 10px;
}
</style>

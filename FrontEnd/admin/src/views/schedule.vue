<template>
  <div class="tab-page-container">
    <el-card class="box-card">
      <div slot="header"
           class="clearfix">
        <span style="line-height: 36px;">班级</span>
        <el-button style="float: right;"
                   type="primary"
                   @click="addOne('classes')">添加新的班级</el-button>
      </div>
      <el-table :data="config.classes"
                border
                style="width: 100%">
        <el-table-column prop="name"
                         label="方向名称">
        </el-table-column>
        <el-table-column prop="major.name"
                         label="方向名称">
        </el-table-column>
        <el-table-column label="操作">
          <template scope="scope">
            <el-button size="small"
                       @click="handleEdit(scope.$index, scope.row)">编辑</el-button>
            <el-button size="small"
                       type="danger"
                       @click="handleDelete(scope.$index, scope.row)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>
    <el-card class="box-card">
      <div slot="header"
           class="clearfix">
        <span style="line-height: 36px;">系统相关设置</span>
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
                     @click="onSubmit">确认</el-button>
          <el-button @click="onCancel">取消</el-button>
        </el-form-item>
      </el-form>
    </el-card>
  
  </div>
</template>
<script>

export default {
  name: "info-page",
  data() {
    return {
      config: {}
    }
  },
  created() {
    this.init()
  },
  methods: {
    init() {
      this.$http.get('config').then(res => {
        this.config = res.data.config
        // debugger
      })
    },
    handleSelect(key, keyPath) {
      console.log(key, keyPath);
    },
    onSubmit() {
      this.$http.post('config/edit', this.config)
    },
    onCancel() {
      this.init()
    }
  }
}

</script>
<style>

</style>

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
      <el-table :data="config.major"
                border
                style="width: 100%">
        <el-table-column prop="created_at" label="创建时间">
        </el-table-column>
        <el-table-column prop="name"
                         label="专业名称">
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
        <span style="line-height: 36px;">专业方向</span>
        <el-button style="float: right;"
                   type="primary"
                   @click="addOne('direction')">添加新的专业方向</el-button>
      </div>
      <el-table :data="config.direction"
                border
                style="width: 100%">
        <el-table-column prop="name"
                         label="方向名称">
        </el-table-column>
        <el-table-column label="隶属专业">
          <template scope="scope">
            <span style="margin-left: 10px"
                  v-for="major in scope.row.major">{{ major.name }}</span>
          </template>
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
  </div>
</template>
<script>

export default {
  name: "info-page",
  data() {
    return {
      config: {

      },
    }
  },
  created() {
    this.$http.get('info').then(res => {
      this.config = res.data
    })
  },
  methods: {
    addOne(type) {

    },
    handleEdit() {

    },
    handleDelete() {

    }
  }
}

</script>
<style>

</style>

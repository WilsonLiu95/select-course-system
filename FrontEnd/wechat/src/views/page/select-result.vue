<template>
  <div class='select-result'
       v-if='isInit'>
    <!-- 专业方向选修课结果-->
    <div class='direction-course'>
      <h2 class="title">专业方向选修课</h2>
      <div v-if="direction_course.length">
        <mt-cell v-for="(item,index) in direction_course"
                 :title="item.title"
                 :label="'人数:' + item.required_number + ' 学分: '+item.credit">
          {{ item.teacher }}
          <span>index</span>
        </mt-cell>
      </div>
      <div v-else
           class='t-center'>
        <h4>暂无选中课程</h4>
      </div>
      <div v-if="system_config.is_direction_open"
           class='btn-group'>
        <mt-button class='btn'
                   type="primary"
                   @click="jumpPage('handle-course','direction','select')">选课</mt-button>
        <mt-button class='btn'
                   type="danger"
                   @click="jumpPage('handle-course','direction','quit')">退选</mt-button>
      </div>
    </div>
  
    <!-- 公选课选修结果-->
    <div class='common-course'>
      <h2 class="title">公共选修课程</h2>
      <div v-if="common_course.length">
        <mt-cell v-for="(item,index) in common_course"
                 :title="item.title"
                 :label=" '人数:' + item.required_number + ' 学分: '+item.credit">
          {{ item.teacher}}
        </mt-cell>
      </div>
      <div v-else
           class='t-center'>
        <h4>暂无选中课程</h4>
      </div>
      <div v-if="system_config.is_common_open"
           class='btn-group'>
        <mt-button class='btn'
                   type="primary"
                   @click="jumpPage('handle-course', 'common', 'select')">选课</mt-button>
        <mt-button class='btn'
                   type="danger"
                   @click="jumpPage('handle-course', 'common', 'quit')">退选</mt-button>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: "select-course-select",
  data() {
    return {
      isInit: false,
      direction_course: [],
      common_course: [],
      system_config: 0,
    }
  },
  created() {
    this.getCanSelectCourse();
  },
  methods: {
    getCanSelectCourse() {
      this.$http.get("select-result").then((res) => {
        this.isInit = true
        this.system_config = res.data.system_config
        this.common_course = res.data.common_course
        this.direction_course = res.data.direction_course
      })
    },
    jumpPage(name, paramsFir, paramsSec) {
      this.$router.push({
        name: name,
        params: {
          0: paramsFir,
          1: paramsSec
        }
      })
    }
  },
}

</script>
<style>
.select-result .title {
  margin: 10px 0;
  text-align: center;
}

.select-result .btn-group {
  /*margin: 8px 0 20px 0;*/
  padding: 10px 0 20px 0;
  display: flex;
  justify-content: flex-end;
}

.select-result .btn-group .btn {
  margin-right: 5px;
}

.select-result .course-check-list .mint-checkbox-label {
  font-size: 12px;
}

.select-result .mint-cell-text {
  font-size: 14px;
}

.select-result .mint-cell-label {
  /*font-size: 12px;*/
}

.select-result .mint-cell-value {
  font-size: 12px;
}
</style>
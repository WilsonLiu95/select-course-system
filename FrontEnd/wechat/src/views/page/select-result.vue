<template>
  <div class='select-result' v-if='isInit'>
    <div class='direction-course'>
      <h2 class="title">专业方向选修课</h2>
      <div v-if="direction_course">
        <mt-cell v-for="(item,index) in direction_course" :title="item.title">
            {{ item.teacher }}
            <span>index</span>
        </mt-cell>
      </div>
      <div v-else class='t-center'>
        <h4>暂无选中课程</h4>
      </div>
      <div v-if="system_status==1" class='btn-group'>
        <mt-button class='btn' type="primary"  @click="$router.push({name:'direction-course-select'})">选课</mt-button>
        <mt-button class='btn' type="danger"  @click="$router.push({name:'direction-course-quit'})" >退选</mt-button>
      </div>
    </div>
    <div v-if="system_status!=1" class='common-course'>
      <h2 class="title">公共选修课程</h2>
      <div v-if="common_course">
        <mt-cell v-for="(item,index) in common_course" :title="item.title">
          {{ item.teacher}}
        </mt-cell>
      </div>
      <div v-else class='t-center'>
        <h4>暂无选中课程</h4>
      </div>
      <div v-if="system_status==2" class='btn-group'>
        <mt-button class='btn' type="primary"  @click="$router.push({name:'common-course-select'})">选课</mt-button>
        <mt-button class='btn' type="danger"  @click="$router.push({name:'common-course-quit'})" >退选</mt-button>
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
        system_status: 0,
      }
    },
    created() {
      this.getCanSelectCourse();
    },
    methods: {
      getCanSelectCourse() {
        this.$http.get("select-result").then((res) => {
          this.isInit = true
          this.system_status = res.data.system_status
          this.common_course = res.data.common_course
          this.direction_course = res.data.direction_course
        })
      },



    },


  }

</script>
<style>
  .select-result .title{
    margin: 10px 0;
    text-align: center;
  }
  .select-result .btn-group {
    margin: 8px 0;
    display: flex;
    justify-content: flex-end;
  }
  .select-result .btn-group .btn{
    margin-right: 5px;
  }
  .select-result .course-check-list .mint-checkbox-label{
    font-size: 12px;
  }
</style>
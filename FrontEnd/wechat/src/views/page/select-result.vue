<template>
  <div class='select-result' v-if='isInit'>
    <h2 class="title">选课结果</h2>
    <div class='direction-course'>
      <mt-cell v-for="(item,index) in direction_course" :title="item.title">
        {{ item.teacher}}
      </mt-cell>
      <div class='btn-group'>
        <mt-button class='btn' type="primary"  @click="$router.push({name:'direction-course-select'})">选课</mt-button>
        <mt-button class='btn' type="danger"  @click="$router.push({name:'direction-course-quit'})" >退选</mt-button>
      </div>
    </div>
    <div class='common-course'>
      <mt-cell v-for="(item,index) in common_course" :title="item.title">
        {{ item.teacher}}
      </mt-cell>
      <div class='btn-group'>
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
        common_course: []
      }
    },
    created() {
      this.getCanSelectCourse();
    },
    methods: {
      getCanSelectCourse() {
        this.$http.get("select-result").then((res) => {
          this.isInit = true
          this.commom_course = res.data.commom_course
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
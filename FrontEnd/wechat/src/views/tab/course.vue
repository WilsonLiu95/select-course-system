<template>
  <div class="tab-page-container tab-course">
    <div class="nav tab-line">
      <mt-button class="t-btn btn" type="primary" @click.native="jumpPage(-1)" size="small">
        上个方向
      </mt-button>
      <mt-button class="t-center-btn btn" type="primary" @click.native="isPopupVisible = true" size="small" v-if="isInit">{{current_page}}/{{direction.length}} {{direction[current_page-1]}}</mt-button>
      <mt-button class="t-btn btn" type="primary" @click.native="jumpPage(+1)" size="small">
        下个方向
      </mt-button>
    </div>
    <!--end 下一页-->

    <!--start 课表清单-->
    <div  v-if="isInit">
      <mt-cell v-if="current_page==1" style="text-align:center" label="提示：部分公共选修课程同时属于专业方向选修课。">
      </mt-cell>
      <mt-cell v-for="(item,index) in course[current_page-1].course" :title="item.title" :label="getLabel(item)" :to="'/details/' + (current_page-1) +'/'+ index" is-link>
        {{ item.teacher}}
      </mt-cell>
    </div>
    <!--end 课表清单-->
    <!--start 选页面弹窗-->
    <mt-popup v-model="isPopupVisible" position="bottom" class="mint-popup" v-if="isInit">
      <!--这里v-if="isInit"的功能在于首次进入页面时，禁止调用pickerPage函数，以防止其将current_page重置为1-->
      <mt-picker :slots="slots" @change="pickerPage" :visible-item-count="5"></mt-picker>
    </mt-popup>
    <!--end 选页面弹窗-->
  </div>
</template>
<script>
  export default {
    name: "course-tab",
    data() {
      return {
        isInit: false,
        first_into: true,
        isPopupVisible: false,

        current_page: 1,
        total_page: 1,
        course: [],
        direction: [], // 存储用户当前被允许选择的方向
        slots: [
          {
            flex: 1,
            values: ['tesy'],
            textAlign: 'center'
          }
        ],
      }
    },
    created() {
      if (_store.hasStoreCourse) {
        // 如果存储过,则直接拿_store.tab_course中的数据进行初始化
        this.initData(_store.course)
      } else {
        this.getCourse()
      }
      // 为了达到记忆用户在哪个页面的功能，将page保存在全局变量中

    },
    methods: {
      getCourse() {
        this.$http.get("/course").then((res) => {
          this.initData(res.data)
          // 存储在全局中，挡掉之后的请求
          _store.course = res.data
          _store.hasStoreCourse = true
        })
      },
      initData(course) {
        // 根据方向和课程初始化数据
        this.total_page = course.length

        var dir = [];
        course.forEach((item,index)=>{
           dir[index] = item['name']
        })
        this.slots[0].values = dir
        this.direction = dir
        this.course = course
        this.isInit = true
        if (_store.page) {
          this.current_page = _store.page
        }
      },
      getLabel(item) {        
          return '  人数:' + item.required_number + ' 学分:' + item.credit
      },
      pickerPage(picker, $item) {
        if (this.first_into) {
          // 第一次进入时，首先会执行一次，会与_store.page中的数据冲突
          this.first_into = false
        } else {
          this.current_page = this.direction.indexOf($item[0]) + 1
          _store.page = this.current_page
        }

      },
      jumpPage(n) {
        if (n == 1 && this.current_page == this.direction.length) {
          util.toast({
            message: '已到最后一页',
            duration:1000,
          });
          return
        }
        if (n == -1 && this.current_page == 1) {
          util.toast({
            message: '已到第一页',
            duration:1000,
          });
          return
        }
        this.current_page = this.current_page + n
        _store.page = this.current_page
      },

    },

  };

</script>
<style scoped>
  .tab-line {
    display: flex;
    flex-direction: row;
    justify-content: center;
    background-color: #f6f8fa;
    padding: 8px 0 8px 0;
    margin: 0 0  10px 0;
  }

  .tab-line button {
    margin: 0 1px;
    padding:  0 3px;
  }
  .tab-line .btn {
    height: 40px;
  }
  .mint-popup {
    width: 100%;
  }

  .t-btn {
    width:60px;
    font-size: 12px;
  }

  .t-center-btn {
    white-space:nowrap;
    text-overflow:ellipsis;
    font-size: 12px;
    width: 150px;
  }
</style>

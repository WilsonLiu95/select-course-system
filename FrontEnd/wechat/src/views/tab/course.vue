<template>
  <div class="tab-page-container">
    <div class="nav tab-line">
      <mt-button @click.native="jumpPage(-1)" size="normal">
        <img :src="assets.previous" height="20" width="20" slot="icon">
      </mt-button>
      <mt-button @click.native="isPopupVisible = true" size="normal" v-if="isInit">{{current_page}}/{{direction.length}} {{direction[current_page-1]}}</mt-button>
      <mt-button @click.native="jumpPage(+1)" size="normal">
        <img :src="assets.next" height="20" width="20" slot="icon">
      </mt-button>
    </div>
    <!--end 下一页-->

    <!--start 课表清单-->
    <div class="page-tab-container" v-if="isInit">
      <mt-cell v-for="item in course[current_page-1]" :title="item.title" :label="getLabel(item)" :to="'/details/' + item.id" is-link>
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
        assets: {
          previous: require("assets/previous.svg"),
          next: require("assets/next.svg"),
        },
        isInit: false,
        first_into: true,
        isPopupVisible: false,

        current_page: 1,
        total_page: 1,

        course: [],
        direction: [], // 存储用户当前被允许选择的方向
        all_direction: [], // 存储本院系的所有方向
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
      if (_store.tab_course.isValid()) {
        // 如果存储过,则直接拿_store中的数据
        this.initData(_store.tab_course.direction, _store.tab_course.course, _store.tab_course.all_direction)
      } else {
        this.getCourse()
      }

      // 为了达到记忆用户在哪个页面的功能，将page保存在全局变量中

    },
    methods: {
      getCourse() {
        this.$http.get("/course").then((res) => {
          var data = res.data.data
          this.initData(data.direction, data.course, data.all_direction)
          // 存储在全局中，挡掉之后的请求
          _store.tab_course.direction = data.direction
          _store.tab_course.course = data.course
          _store.tab_course.all_direction = data.all_direction
        })
      },
      initData(direction, course, all_direction) {
        // 根据方向和课程初始化数据
        this.direction = direction
        this.all_direction = all_direction
        this.total_page = direction.length
        this.slots[0].values = direction
        this.course = course
        this.isInit = true
        if (_store.tab_course.page) {
          // debugger
          this.current_page = _store.tab_course.page
        }
      },
      getLabel(item) {
        if (this.current_page == 1 && item.direction_code !== 0) {
          // 另外再显示课程归属于哪些方向
          return this.all_direction[item.direction_code] + '  人数:' + item.required_number
        } else {
          // 如果不是在公共页面则直接显示课程人数
          return '  人数:' + item.required_number
        }

      },
      pickerPage(picker, $item) {
        if (this.first_into) {
          // 第一次进入时，首先会执行一次，会与_store.page中的数据冲突
          this.first_into = false
        } else {
          this.current_page = this.direction.indexOf($item[0]) + 1
          _store.tab_course.page = this.current_page
        }

      },
      jumpPage(n) {
        if (n == 1 && this.current_page == this.direction.length) {
          util.toast({
            message: '已到最后一页',
          });
          return
        }
        if (n == -1 && this.current_page == 1) {
          util.toast({
            message: '已到第一页',
          });
          return
        }
        this.current_page = this.current_page + n
        _store.tab_course.page = this.current_page
      },

    },

  };

</script>
<style>
  .tab-line {
    display: flex;
    flex-direction: row;
    justify-content: center;
    background-color: #f6f8fa;
    padding: 5px 0 5px 0;
  }

  .tab-line button {
    margin: 0 2px;
  }

  .mint-popup {
    width: 100%;
  }
</style>

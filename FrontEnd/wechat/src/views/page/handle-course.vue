<template>
  <div class="handle-course">
    <div class='t-center'>
      <h4 v-if='isCommonCourse'><span v-if="isQuit">退选</span>公共选修课程</h4>
      <h4 v-else><span v-if="isQuit">退选</span>专业方向选修课</h4>
    </div>
    <div>
      <mt-checklist v-if="canSelectCourseOptions.length"
                    title="课程列表"
                    v-model="finalCourseArr"
                    :options="canSelectCourseOptions">
      </mt-checklist>
    </div>
  
    <div class='btn-group'
         @click="confirm">
      <mt-cell title="提示:满足学分要求才可以提交"
               :label="'学分要求:' +  (isCommonCourse ? (system_config.min_common_credit + '~' + system_config.max_common_credit ):
                (system_config.min_direction_credit + '~' + system_config.max_direction_credit))">
        <span v-if='isQuit'>
          {{ '退选:' + this.currentTotalCredit  + "学分"}}
        </span>        
        <span v-else>
          {{ '当前: ' + this.currentTotalCredit }}
        </span>
      </mt-cell>
      <mt-button v-if="isQuit"
                 type="danger"
                 size="large"
                 :disabled='isDisabledSubmit'>退选</mt-button>
      <mt-button v-else
                 type="primary"
                 size="large"
                 :disabled='isDisabledSubmit'>选课</mt-button>
    </div>
  
  </div>
</template>
<script>
export default {
  name: "handle-course",
  data() {
    return {
      isQuit: false,
      isCommonCourse: false,
      isAbleHandle: true, // 是否允许操作

      system_config: {},
      currentTotalCredit: 0,

      canSelectCourse: [],
      canSelectCourseOptions: [],
      has_select_course: [],
      finalCourseArr: [],
    }
  },
  created() {
    this.isCommonCourse = (this.$route.params[0] == 'common') // 判断是否为退选页面
    this.isQuit = (this.$route.params[1] == 'quit')
    this.getCanSelectCourse();
  },
  computed: {
    isDisabledSubmit: function () {
      // debugger
      var isDisabledSubmit = true
      var currentTotalCredit = 0
      this.canSelectCourse.forEach((item, index) => {
        if (this.finalCourseArr.indexOf(item.id) != -1) { // 课程被选中
          currentTotalCredit += item.credit
        }
      })
      this.currentTotalCredit = currentTotalCredit // 计算当前学分
      if (this.isQuit) { // 退选，则不可以退选0门课程
        isDisabledSubmit = this.finalCourseArr.length == 0
      } else { // 选课，则需要多选几门课程
        // 首先需要多选几门课程，才允许提交
        isDisabledSubmit = this.has_select_course.toString() == this.finalCourseArr.toString()
        // 其次，需满足最低学分要求
        var minCredit = this.isCommonCourse ? this.system_config.min_common_credit : this.system_config.min_direction_credit
        var maxCredit = this.isCommonCourse ? this.system_config.max_common_credit : this.system_config.max_direction_credit
        if (currentTotalCredit < minCredit || currentTotalCredit > maxCredit) {
          isDisabledSubmit = true
        }
      }
      return isDisabledSubmit
    }
  },
  methods: {
    initPage(data) {
      this.canSelectCourse = data.courseList
      this.finalCourseArr = data.has_select_course
      this.has_select_course = data.has_select_course
      this.system_config = data.system_config

      this.canSelectCourseOptions = this.makeCourseOption(data.courseList, data.has_select_course, this.isQuit)
    },
    getCanSelectCourse() {
      this.$http.get("handle-course/can-select-course?is_common=" + this.isCommonCourse,{
          xsrfCookieName: 'XSRF-TOKEN', // default
          xsrfHeaderName: 'X-XSRF-TOKEN', // default
      }).then((res) => {
        this.initPage(res.data);
      })
    },
    getSelectResult() {
      setTimeout(() => {
        this.$http.get('handle-course/select-result', {
          noIndicator: true,
          params: {
            is_common: this.isCommonCourse
          }
        }).then(res => {
          if (res.data.isFinish === false) {
            return this.getSelectResult() // 没有结束处理，继续请求
          }
          // 请求成功，结束loading动画
          this.$indicator.close()
          this.isAbleHandle = true // 重新允许操作
          this.initPage(res.data.data) // 根据当下数据重新渲染页面

          // 弹出提示
          var msg = '操作成功'
          for (var key in res.data.result) {
            if (res.data.result[key] == false) {   // 只有有课程失败就弹出提示
              msg = '有部分课程，操作失败，请再次尝试'
            }
          }
          util.box.alert(msg)
        }).catch(err => {
          this.isAbleHandle = true
          this.$indicator.close()
        })
      }, 1000)
    },
    confirm() {
      if (!this.isAbleHandle) {
        return // 不允许重复操作
      }
      var msg = this.isQuit ? "退选" : "选中"
      util.box.confirm("确定" + msg + "勾选的课程").then(action => {
        this.isAbleHandle = false// 禁止之后再进行操作
        this.$indicator.open({ // 先弹起loading动画
          text: '紧急' + msg + 'ing~',
          spinnerType: 'double-bounce'
        })
        this.$http.post("handle-course/handle-course", {
          course_id_arr: this.finalCourseArr,
          is_common: this.isCommonCourse,
          isQuit: this.isQuit // 告诉后台，这次是选课还是退选课程
        }, { noIndicator: true })// 配置项，取消默认ajax请求的loading动画
          .then((res) => {
            setTimeout(() => { // 首次发起请求结果
              this.getSelectResult() // 查询结果
            }, 500)
          })
          .catch(err => {
            this.isAbleHandle = true
            this.$indicator.close()
          })
      }, action => {
        util.toast("您已取消操作")
      })
    },
    makeCourseOption(canSelectCourse, finalCourseArr, isQuit) {
      var data = [];
      canSelectCourse.forEach((item, index) => {
        var option = {
          label: " 学分:" + item.credit + " 人数" + item.current_number + "/" + item.required_number + " " + "《" + item.title + "》",
          value: item.id
        }
        var isContainer = (finalCourseArr.indexOf(item.id) != -1)
        if (isQuit) {
          option.disabled = !isContainer // 退选则只要是用户选中的课程，就可以退选
        } else { // 选课
          option.disabled = isContainer // 选课首先，用户要是已选了该门课程则置为true
          if (item.current_number == item.required_number) { // 另外如果该门课满员了，也置为true
            option.disabled = true
          }
        }
        data.push(option)
      })
      return data
    },
  },
}

</script>

<style>
.handle-course .mint-checkbox-label {
  font-size: 12px;
}

.handle-course .btn-group {
  padding: 10px 0 10px 0;
}

.handle-course .mint-cell-text {
  font-size: 14px;
}

.handle-course .mint-cell-value {
  font-size: 14px;
}
</style>
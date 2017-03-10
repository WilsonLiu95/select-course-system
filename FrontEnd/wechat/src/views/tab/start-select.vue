<template>
  <div class="tab-page-container">
    <div class='section-part'>
      <mt-field label="院系" v-model="account.institute" placeholder="电信学院" disabled></mt-field>
      <mt-field v-if="account.major" label="专业" v-model="account.major" placeholder="通信工程" disabled></mt-field>
      <mt-field label="班级" v-model="account.classes" placeholder="通信1305班" disabled></mt-field>
      <mt-field label="姓名" v-model="account.name" placeholder="WilsonLiu" disabled></mt-field>
      <mt-field label="工号" v-model="account.job_num" placeholder="U19951995" disabled></mt-field>
    </div>
    <div class='section-part t-center'>
      <div>
        <h4>请确认以上信息，确认后开始选课。</h4>
        <mt-button size='large' type="primary" @click="$router.push({name:'select-direction'})">开始选课</mt-button>
      </div>
    </div>

  </div>
</template>
<script>
  export default {
    name: "account-page",
    data() {
      return {
        account: {
          'major': "待选择"
        },
      }
    },
    created() {
      if (_store.account && _store.account.id) {
        this.initData(_store.account)
      } else {
        this.getAccount();
      }
    },
    methods: {
      getAccount() {
        this.$http.get("account").then((res) => {
          var data = res.data
          this.initData(data.account)
          // 更新到全局变量中
          _store.account = data.account
        })
      },
      initData(account) {
        this.account = account
      }
    },
  }

</script>
<style scoped>
  .mint-cell-wrapper button {
    margin: 7px 0 0 0;
  }
  /*.section-part {
    margin: 0 0 5px 0;
  }*/
</style>

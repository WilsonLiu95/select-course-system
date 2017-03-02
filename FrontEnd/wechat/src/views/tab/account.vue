<template>
  <div class="tab-page-container">
    <div>
      <mt-field label="院系" v-model="account.institute" placeholder="电信学院" disabled></mt-field>
      <mt-field label="专业" v-model="account.major" placeholder="通信工程" disabled></mt-field>
      <mt-field label="方向" v-model="account.direction" placeholder="互联网" disabled>
        <mt-button size="small" @click="$router.push({name:'select-direction'})">选择</mt-button>
      </mt-field>
      <mt-field label="班级" v-model="account.classes" placeholder="通信1305班" disabled></mt-field>
      <mt-field label="姓名" v-model="account.name" placeholder="WilsonLiu" disabled></mt-field>
      <mt-field label="工号" v-model="account.job_num" placeholder="U19951995" disabled></mt-field>
    </div>

  </div>
</template>
<script>
  export default {
    name: "account-page",
    data() {
      return {
        account: {},
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
          var data = res.data.data
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
</style>

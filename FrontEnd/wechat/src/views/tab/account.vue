<template>
  <div class="tab-page-container">
    <div>
      <mt-field label="院系" v-model="account.institute" placeholder="电信学院" disabled></mt-field>
      <mt-field label="专业" v-model="account.major" placeholder="通信工程" disabled></mt-field>
      <mt-field label="方向" v-model="account.direction" placeholder="互联网" disabled></mt-field>
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
        isDiabled: true,
        account: {},
      }
    },
    computed: {
    },
    methods: {
      getAccount() {
        this.$http.get("account").then((res) => {
          this.account = res.data.data
        })
      },

    },
    created() {
      this.getAccount();
    }

  }

</script>
<style>

</style>

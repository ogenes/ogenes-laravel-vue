<template>
  <div>
    <el-tabs tab-position="left" :style="`height: ` + tableHeight + `px;`" :stretch="true" type="border-card">
      <el-tab-pane label="个人资料">
        <user-info
          :table-height="tableHeight"
          :account="account"
          :name="name"
          :mobile="mobile"
          :email="email"
          :avatar="avatar"
        />
      </el-tab-pane>
      <el-tab-pane label="修改密码">
        <update-pass/>
      </el-tab-pane>
      <el-tab-pane label="操作日志">
        <user-log :table-height="tableHeight"/>
      </el-tab-pane>
    </el-tabs>
  </div>
</template>

<script>
  import {mapGetters} from 'vuex'
  import updatePass from './components/update-pass';
  import userInfo from './components/user-info';
  import userLog from './components/user-log';

  export default {
    name: "UserProfile",
    components: {
      updatePass,
      userInfo,
      userLog,
    },
    computed: {
      ...mapGetters([
        'name',
        'account',
        'mobile',
        'email',
        'avatar',
      ])
    },
    mounted() {
      this.$nextTick(() => {
        this.tableHeight = window.innerHeight - 85;
      })
    },
    created() {
      console.log(this.name, this.avatar, this.mobile, this.account, this.email, 'userinfo');
    },
    data() {
      return {
        tableHeight: 0,
      }
    }
  }
</script>

<style scoped>

</style>

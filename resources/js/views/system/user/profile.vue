<template>
  <div>
    <el-tabs tab-position="left" :style="`height: ` + tableHeight + `px`" :stretch="true" type="border-card">
      <el-tab-pane label="个人资料">用户资料</el-tab-pane>
      <el-tab-pane label="账号设置">账号</el-tab-pane>
      <el-tab-pane label="修改密码">
        <update-pass/>
      </el-tab-pane>
      <el-tab-pane label="操作日志">操作日志</el-tab-pane>
    </el-tabs>
  </div>
</template>

<script>
  import updatePass from './components/update-pass';

  export default {
    name: "UserProfile",
    components: {
      updatePass
    },
    mounted() {
      this.$nextTick(() => {
        this.tableHeight = window.innerHeight - 85;
      })
    },
    data() {
      return {
        tableHeight: 0
      }
    }
  }
</script>

<style scoped>

</style>

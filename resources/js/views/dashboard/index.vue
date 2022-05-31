<template>
  <div>
    <el-col :span="14">
      <el-card v-if="checkPermission(['DashboardUserGroup'])" class="home-item-card">
        <UserGroup />
      </el-card>
      <el-card class="home-item-card">
        <Hello/>
      </el-card>
    </el-col>
    <el-col :span="10">
      <el-card class="home-item-card">
        <Calendar/>
      </el-card>
      <el-card class="home-item-card">
        <div slot="header">
          意见反馈
        </div>
        <Feedback/>
      </el-card>
    </el-col>
  </div>
</template>

<script>

  import Hello from './components/hello';
  import Calendar from './components/calendar';
  import Task from './components/task';
  import Feedback from './components/feedback';
  import UserGroup from './components/user-group';
  import checkPermission from "@/utils/permission";

  export default {
    name: 'Dashboard',
    components: {
      Hello,
      Calendar,
      Task,
      Feedback,
      UserGroup,
    },
    data() {
      return {
        checkPermission
      }
    },
    computed: {},
    created() {
    }
  }
</script>

<style scoped lang="scss">
  .home-item-card {
    margin: 10px;
    overflow: auto;
    height: 100%;
  }
</style>

<template>
  <div>
    <el-col :span="18">
      <el-card class="home-card">
        <el-card class="home-item-card">
          <Hello/>
        </el-card>
        <el-card class="home-item-card">
          <UserStatistics/>
        </el-card>
      </el-card>
    </el-col>
    <el-col :span="6">
      <el-card class="home-card">
        <Calendar/>
      </el-card>
    </el-col>
    <el-col :span="12">
      <el-card class="home-card">
        <Feedback/>
      </el-card>
    </el-col>
    <el-col :span="12">
      <el-card class="home-card">
        <Task/>
      </el-card>
    </el-col>
  </div>
</template>

<script>

  import Hello from './components/hello';
  import Calendar from './components/calendar';
  import Task from './components/task';
  import Feedback from './components/feedback';
  import UserStatistics from './components/user-statistics';

  export default {
    name: 'Dashboard',
    components: {
      Hello,
      Calendar,
      Task,
      Feedback,
      UserStatistics,
    },
    data() {
      return {}
    },
    computed: {},
    created() {
    }
  }
</script>

<style scoped lang="scss">
  .home-card {
    margin: 10px;
    height: 800px;
    .home-item-card {
      height: 360px;
      margin: 10px;
    }
  }
</style>

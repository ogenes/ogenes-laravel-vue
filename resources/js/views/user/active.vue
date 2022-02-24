<template>
  <div class="active-container">
    <el-card>
      <div class="title">
        <h1>激活用户</h1>
      </div>
      <div class="active-content">
        <div class="active-detail">
          <p>Hi, {{ email }}</p>
          <p :style="ret > 0 ? '' : 'color:red;'">{{ msg }}</p>
          <br/>
          <el-button type="text" @click="closePage">关闭页面</el-button>
        </div>
      </div>
    </el-card>
  </div>
</template>

<script>
  import axios from '@/utils/axios';

  const activeUrl = '/api/auth/activeUser';
  export default {
    name: "active",
    data() {
      return {
        ret: 0,
        email: '',
        msg: '',
      }
    },
    created() {
      axios.post(activeUrl, {code: this.$route.query.code}).then((res) => {
        if (res.data.code > 0) {
          this.msg = res.data.msg;
        } else {
          const ret = res.data.data;
          this.ret = 1;
          this.email = ret.email;
          this.msg = ret.msg;
        }
      }).catch((e) => {
        this.msg = '激活失败';
        console.log('error:', e.message);
      });
    },
    methods: {
      closePage() {
        window.opener = null;
        window.open("about:blank", "_top").close()
      }
    }
  }
</script>


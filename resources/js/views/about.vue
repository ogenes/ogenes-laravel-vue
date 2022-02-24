<template>
  <div class="about-container">
    <el-card>
      <h1>严禁上传及分享如下类型的图片：</h1>
      <ul class="notice-content">
        <li>含有色情、暴力、宣扬恐怖主义的图片</li>
        <li>侵犯版权、未经授权的图片</li>
        <li>其他违反中华人民共和国法律的图片</li>
      </ul>
    </el-card>
  </div>
</template>

<script>
  export default {
    name: 'About',
    data() {
      return {}
    },
    watch: {},
    created() {
    },

    methods: {},
  };
</script>
<style lang="less">
  .about-container {
    height: 100%;

    .el-card {
      height: 100%;
    }

    .notice-content {
      margin-left: 20px;
    }
  }
</style>

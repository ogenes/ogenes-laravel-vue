<template>
  <el-form label-width="80px" :model="formLabelAlign">
    <el-form-item label="名称">
      <el-input v-model="formLabelAlign.name"></el-input>
    </el-form-item>
    <el-form-item label="活动区域">
      <el-input v-model="formLabelAlign.region"></el-input>
    </el-form-item>
    <el-form-item label="活动形式">
      <el-input v-model="formLabelAlign.type"></el-input>
    </el-form-item>
  </el-form>
</template>

<script>
  export default {
    name: "",
    data() {
      return {
        formLabelAlign: {
          name: '',
          region: '',
          type: ''
        }
      };
    },
  }
</script>

<style scoped>

</style>

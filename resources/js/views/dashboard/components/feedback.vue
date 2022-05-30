<template>
  <el-form ref="form" :rules="rules" :model="params">
    <el-form-item prop="type">
      <el-select v-model="params.type" style="width: 100%">
        <el-option v-for="(v, k) in typeMap" :key="k" :value="k" :label="v"/>
      </el-select>
    </el-form-item>
    <el-form-item prop="content">
      <el-input type="textarea" :rows="6" v-model="params.content"/>
    </el-form-item>
    <el-form-item align="right">
      <el-button @click="add">提交</el-button>
    </el-form-item>
  </el-form>
</template>

<script>
  import {getOptions, add} from "@/api/feedback";

  export default {
    name: "Feedback",
    data() {
      return {
        typeMap: {},
        params: {
          type: '1',
          content: '',
        },
        rules: {
          type: [
            {required: true, message: "请选择类型", trigger: "change"},
          ],
          content: [
            {required: true, message: "请输入建议", trigger: "blur"},
          ],
        },
      };
    },

    async created() {
      const ret = await getOptions();
      this.typeMap = ret?.data?.typeMap;
    },

    methods: {
      add() {
        this.$refs.form.validate(valid => {
          if (valid) {
            add(this.params).then((res) => {
              if (res.code > 0) {
                this.$message.error(res.msg)
              } else {
                this.$message.success('提交成功!');
                this.params = {
                  type: '1',
                  content: '',
                };
              }
            })
          }
        });
      },
    }
  }
</script>

<style scoped>

</style>

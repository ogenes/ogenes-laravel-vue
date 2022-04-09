<template>
  <el-card style="width: 80%;">
    <el-form ref="form" :model="user" :rules="rules" :inline="false" label-width="80px" label-position="top">
      <el-form-item label="旧密码" prop="oldPassword">
        <el-input v-model="user.oldPassword" placeholder="请输入旧密码" type="password" show-password/>
      </el-form-item>
      <el-form-item label="新密码" prop="newPassword">
        <el-input v-model="user.newPassword" placeholder="请输入新密码" type="password" show-password/>
      </el-form-item>
      <el-form-item label="确认密码" prop="confirmPassword">
        <el-input v-model="user.confirmPassword" placeholder="请确认密码" type="password" show-password/>
      </el-form-item>
      <el-form-item>
        <el-button type="primary" size="mini" @click="submit">保存</el-button>
      </el-form-item>
    </el-form>
  </el-card>
</template>

<script>
  import {updatePass} from "@/api/user";

  export default {
    data() {
      const equalToPassword = (rule, value, callback) => {
        if (this.user.newPassword !== value) {
          callback(new Error("两次输入的密码不一致"));
        } else {
          callback();
        }
      };
      return {
        user: {
          oldPassword: undefined,
          newPassword: undefined,
          confirmPassword: undefined
        },
        // 表单校验
        rules: {
          oldPassword: [
            {required: true, message: "旧密码不能为空", trigger: "blur"}
          ],
          newPassword: [
            {required: true, message: "新密码不能为空", trigger: "blur"},
            {min: 6, max: 20, message: "长度在 6 到 20 个字符", trigger: "blur"}
          ],
          confirmPassword: [
            {required: true, message: "确认密码不能为空", trigger: "blur"},
            {required: true, validator: equalToPassword, trigger: "blur"}
          ]
        }
      };
    },
    methods: {
      submit() {
        this.$refs.form.validate(valid => {
          if (valid) {
            updatePass(this.user).then((res) => {
              if (res.code > 0) {
                this.$message.error(res.msg)
              } else {
                this.$message.success('操作成功');
                this.logout();
                //
              }
            })
          } else {
            console.log('error submit!!');
            return false
          }
        });
      },
      async logout() {
        await this.$store.dispatch('user/logout');
        await this.$router.push(`/login?redirect=${this.$route.fullPath}`);
      }
    }
  };
</script>

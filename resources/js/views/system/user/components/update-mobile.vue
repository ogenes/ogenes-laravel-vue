<template>
  <div>
    <el-form
      ref="bindForm"
      :model="bindForm"
      :rules="bindFormRules"
      label-position="right"
      label-width="100px"
      style="margin-right: 20%">
      <el-form-item label="手机号" prop="mobile">
        <el-input v-model="bindForm.mobile" placeholder="请输入手机号"/>

      </el-form-item>
      <el-form-item label="验证码" prop="code">
        <el-input placeholder="请输入验证码" v-model="bindForm.code">
          <template slot="append">
            <span v-if="sendCodeButton.disabled" style="cursor: not-allowed"> 已发送 {{ sendCodeButton.count }}</span>
            <span v-else style="cursor: pointer" @click="sendCode"> 获取验证码 </span>
          </template>
        </el-input>
      </el-form-item>
      <el-form-item label=" ">
        <div style="text-align: right">
          <el-button @click="closeDialog"> 取消 </el-button>
          <el-button type="primary" @click="updateMobile"> 提交 </el-button>
        </div>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
  import {
    updateMobile,
    sendCode,
  } from "@/api/user";
  export default {
    name: "",

    props: {
      closeDialog: {
        type: Function,
        default: null
      },
    },
    created() {
      this.bindForm.mobile = this.$store.getters.mobile;
    },

    data() {
      return {
        bindForm: {
          mobile: '',
          code: ''
        },
        bindFormRules: {
          mobile: [{required: true, message: '必选项！', trigger: 'blur'}],
          code: [{required: true, message: '必选项！', trigger: 'blur'}]
        },
        sendCodeButton: {
          timer: null,
          disabled: false,
          count: 59,
          countdown: 59
        },
      }
    },
    methods: {
      sendCode() {
        if (!this.bindForm.mobile) {
          this.$message.error('请输入手机号！');
          return;
        }
        sendCode({mobile: this.bindForm.mobile}).then((res) => {
          if (res.data.code > 0) {
            this.msg = res.data.msg;
          } else {
            this.startTimer()
          }
        }).catch((e) => {
          this.msg = '验证码发送失败！';
          console.log('error:', e.message);
        });
      },
      startTimer() {
        if (!this.timer) {
          this.sendCodeButton.disabled = true;
          this.sendCodeButton.count = this.sendCodeButton.countdown;
          this.timer = setInterval(() => {
            if (this.sendCodeButton.count > 1) {
              this.sendCodeButton.count--;
            } else {
              this.sendCodeButton.disabled = false;
              clearInterval(this.timer);
              this.timer = null;
            }
          }, 1000);
        }
      },
      updateMobile() {
        this.$refs.bindForm.validate(valid => {
          if (valid) {
            updateMobile(this.bindForm).then((res) => {
              if (res.code > 0) {
                this.$message.error(res.msg)
              } else {
                this.$message.success('修改成功');
                this.$store.commit('user/SET_MOBILE', this.bindForm.mobile);
                this.closeDialog();
              }
            }).catch((e) => {
              console.log('error submit!')
            })
          } else {
            console.log('error submit!!');
            return false
          }
        });
      }
    },
  }
</script>

<style scoped>

</style>

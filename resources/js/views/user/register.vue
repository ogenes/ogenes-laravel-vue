<template>
  <div class="register-container">
    <el-form
      ref="userInfo"
      :model="userInfo"
      :rules="userRules"
      class="register-form"
      auto-complete="on"
      label-position="left">
      <div class="title-container">
        <h3 class="title"><i class="my-icon-user"></i> {{ $t('user.login.data.register') }}</h3>
      </div>
      <el-form-item prop="username">
        <span class="svg-container">
          <i class="my-icon-user"></i>
        </span>
        <el-input ref="username" v-model="userInfo.username" :placeholder="$t('user.login.data.username')"
                  name="username" type="text"
                  tabindex="1" auto-complete="on"/>
      </el-form-item>
      <el-form-item prop="email">
        <span class="svg-container">
          <i class="my-icon-email"></i>
        </span>
        <el-input ref="email" v-model="userInfo.email" :placeholder="$t('user.login.data.email')" name="email"
                  type="text" tabindex="2"
                  auto-complete="on"/>
      </el-form-item>
      <el-form-item prop="password">
        <span class="svg-container">
          <i class="my-icon-password"></i>
        </span>
        <el-input :key="passwordType" ref="password" v-model="userInfo.password" :type="passwordType"
                  :placeholder="$t('user.login.data.password')" name="password" tabindex="3" auto-complete="on"/>
      </el-form-item>
      <el-form-item prop="confirmPassword">
        <span class="svg-container">
          <i class="my-icon-password"></i>
        </span>
        <el-input :key="passwordType" ref="confirmPassword" v-model="userInfo.confirmPassword"
                  :type="passwordType" :placeholder="$t('user.login.data.confirmPassword')" name="confirmPassword"
                  tabindex="4"
                  auto-complete="on" @keyup.enter.native="handleRegister"/>
        <span class="show-pwd" @click="showPwd">
          <i :class="passwordType === 'password' ? 'my-icon-eye' : 'my-icon-eye-open'"/>
        </span>
      </el-form-item>
      <br>
      <el-button :loading="loading" type="primary" style="width:100%;" @click.native.prevent="handleRegister">
        {{ $t('user.login.data.register') }}
      </el-button>
      <a>
        <router-link to="/Users/login">
          {{ $t('user.login.data.registered') }}
        </router-link>
      </a>
    </el-form>
  </div>
</template>

<script>
  import axios from '@/utils/axios';
  import {validStr} from '@/utils/validate'

  const registerApi = '/api/auth/register';
  export default {
    name: "Register",
    data() {
      const validateEmail = (rule, value, callback) => {
        if (!validStr('email', value)) {
          callback(new Error('请输入正确的邮箱'))
        } else {
          callback()
        }
      };
      const validatePassword = (rule, value, callback) => {
        if (!validStr('password', value)) {
          callback(new Error('密码至少6位!'))
        } else {
          callback()
        }
      };
      const validateConfirmPassword = (rule, value, callback) => {
        if (!validStr('password', value)) {
          callback(new Error('密码至少6位!'))
        } else if (value !== this.userInfo.password) {
          callback(new Error('两次输入密码不一致!'))
        } else {
          callback()
        }
      };
      return {
        loading: false,
        passwordType: 'password',
        redirect: undefined,
        userInfo: {
          username: '',
          email: '',
          password: '',
          confirmPassword: '',
        },
        userRules: {
          username: [{required: true, trigger: 'blur', message: '必填'}],
          email: [{required: true, trigger: 'blur', validator: validateEmail}],
          password: [{required: true, trigger: 'blur', validator: validatePassword}],
          confirmPassword: [{required: true, trigger: 'blur', validator: validateConfirmPassword}]
        },
      }
    },
    methods: {
      showPwd() {
        if (this.passwordType === 'password') {
          this.passwordType = ''
        } else {
          this.passwordType = 'password'
        }
        this.$nextTick(() => {
          this.$refs.password.focus()
        })
      },
      handleRegister() {
        this.$refs.userInfo.validate(valid => {
          if (valid) {
            axios.post(registerApi, this.userInfo).then((res) => {
              if (res.data.code > 0) {
                return this.$message.error(res.data.msg);
              } else {
                this.$message.success('注册成功！');
                this.$router.push({
                  path: `/Users/login`,
                })
              }
            }).catch((e) => {
              console.log('error:', e.message);
              return this.$message.error(e.message);
            });
          } else {
            console.log('error submit!!');
            return false
          }
        })
      }
    }
  }
</script>

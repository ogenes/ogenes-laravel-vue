<template>
  <div class="login-container">
    <el-form ref="loginForm" :model="loginForm" :rules="loginRules" class="login-form" auto-complete="on"
             label-position="left">

      <div class="title-container">
        <h3 class="title"><i class="el-icon-lock"/> {{ $t("user.login.data.login") }} </h3>
      </div>

      <el-form-item prop="email">
        <span class="svg-container">
          <i class="my-icon-user"/>
        </span>
        <el-input ref="email" v-model="loginForm.email" :placeholder="$t('user.login.data.email')" name="email"
                  type="text" tabindex="1" auto-complete="on"/>
      </el-form-item>

      <el-form-item prop="password">
        <span class="svg-container">
          <i class="my-icon-password"/>
        </span>
        <el-input :key="passwordType" ref="password" v-model="loginForm.password" :type="passwordType"
                  :placeholder="$t('user.login.data.password')" name="password" tabindex="2" auto-complete="on"
                  @keyup.enter.native="handleLogin"/>
        <span class="show-pwd" @click="showPwd">
          <i :class="passwordType === 'password' ? 'my-icon-eye' : 'my-icon-eye-open'"/>
        </span>
      </el-form-item>
      <div>
        <el-checkbox v-model="loginForm.rememberMe">
          <span style="color: #606266; font-size: 12px;">{{ $t('user.login.data.rememberMe') }}</span>
        </el-checkbox>
      </div>
      <br>
      <el-button :loading="loading" type="primary" style="width:100%;" @click.native.prevent="handleLogin">
        {{ $t("user.login.data.login") }}
      </el-button>
      <a>
        <router-link to="/Users/register">
          {{ $t('user.login.data.register') }}
        </router-link>
      </a>
    </el-form>
  </div>
</template>

<script>
  import {validStr} from '@/utils/validate'

  export default {
    name: 'Login',
    data() {
      const validateEmail = (rule, value, callback) => {
        if (!validStr('email', value)) {
          callback(new Error('请输入正确的邮箱'))
        } else {
          callback()
        }
      }
      const validatePassword = (rule, value, callback) => {
        if (!validStr('password', value)) {
          callback(new Error('密码至少6位!'))
        } else {
          callback()
        }
      }
      return {
        loginForm: {
          email: '',
          password: '',
          rememberMe: false
        },
        loginRules: {
          email: [{required: true, trigger: 'blur', validator: validateEmail}],
          password: [{required: true, trigger: 'blur', validator: validatePassword}]
        },
        loading: false,
        passwordType: 'password',
        redirect: undefined
      }
    },
    watch: {
      $route: {
        handler: function (route) {
          this.redirect = route.query && route.query.redirect
        },
        immediate: true
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
      handleLogin() {
        this.$refs.loginForm.validate(valid => {
          if (valid) {
            this.loading = true
            this.$store.dispatch('user/login', this.loginForm).then(() => {
              this.$router.push({
                path: this.redirect || '/'
              });
              this.loading = false
            }).catch((e) => {
              this.$message.error(e.message);
              this.loading = false
            })
          } else {
            console.log('error submit!!')
            return false
          }
        })
      }
    }
  }
</script>

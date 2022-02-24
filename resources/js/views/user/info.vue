<template>
  <div class="user-info-container">
    <el-card>
      <div class="user-info">
        <el-row class="info-row">
          <el-col :span="12">
            <div class="info-title">
              {{ $t("user.profile.data.email") }}:
            </div>
          </el-col>
          <el-col :span="12">
            <div class="info-value">
              Ogenes.yi@gmail.com
            </div>
          </el-col>
        </el-row>
        <el-row class="info-row">
          <el-col :span="12">
            <div class="info-title">
              {{ $t("user.profile.data.username") }}:
            </div>
          </el-col>
          <el-col :span="12">
            <div class="info-value">
              {{ userInfo.username }}
            </div>
          </el-col>
        </el-row>
        <el-row class="info-row">
          <el-col :span="12">
            <div class="info-title">
              {{ $t("user.profile.data.mobile") }}:
            </div>
          </el-col>
          <el-col :span="12">
            <div v-if="userInfo.mobile" class="info-value">
              {{ userInfo.mobile}}
              <el-button type="text" @click="unbind()"> {{ $t("user.profile.data.unbind")}}</el-button>
            </div>
            <div v-else class="info-value">
              _
              <el-button type="text" @click="bindDialog = true">{{ $t("user.profile.data.bind")}}</el-button>
            </div>
          </el-col>
        </el-row>
      </div>
    </el-card>

    <el-dialog
      :visible.sync="bindDialog"
      :show-close="false"
      :close-on-click-modal="false"
      :destroy-on-close="true"
      width="50%">
      <div>
        <el-form
          ref="bindForm"
          :model="bindForm"
          :rules="bindFormRules"
          label-position="right"
          label-width="100px"
          style="margin-right: 20%">
          <el-form-item :label="$t('user.profile.bindForm.mobile')" prop="mobile">
            <el-input v-model="bindForm.mobile" :placeholder="$t('user.profile.bindForm.mobilePlaceholder')"/>

          </el-form-item>
          <el-form-item :label="$t('user.profile.bindForm.code')" prop="code">
            <el-input :placeholder="$t('user.profile.bindForm.codePlaceholder')" v-model="bindForm.code">
              <template slot="append">
                <span v-if="sendCodeButton.disabled" style="cursor: not-allowed">{{ $t("user.profile.bindForm.sent") }} {{ sendCodeButton.count }}</span>
                <span v-else style="cursor: pointer" @click="sendCode">{{ $t("user.profile.bindForm.getCode") }}</span>
              </template>
            </el-input>
          </el-form-item>
          <el-form-item label=" ">
            <div style="text-align: right">
              <el-button @click="bindDialog = false">{{ $t("queryForm.cancel") }}</el-button>
              <el-button type="primary" @click="bind">{{ $t("queryForm.submit") }}</el-button>
            </div>
          </el-form-item>
        </el-form>
      </div>
    </el-dialog>
  </div>
</template>

<script>
  import {mapGetters} from 'vuex'
  import axios from '@/utils/axios';

  const sendCodeUrl = '/api/user/sendBindCode';
  const bindUrl = '/api/user/bindMobile';
  const unbindUrl = '/api/user/unbindMobile';
  export default {
    name: 'Info',
    computed: {
      ...mapGetters([
        'userInfo',
      ]),
    },
    data() {
      return {
        bindDialog: false,
        bindLoading: false,
        bindForm: {
          mobile: '',
          code: ''
        },
        sendCodeButton: {
          timer: null,
          disabled: false,
          count: 59,
          countdown: 59
        },
        bindFormRules: {
          mobile: [{required: true, message: '必选项！', trigger: 'blur'}],
          code: [{required: true, message: '必选项！', trigger: 'blur'}]
        }
      }
    },
    watch: {},
    created() {
      console.log('init')
    },

    methods: {
      sendCode() {
        if (!this.bindForm.mobile) {
          this.$message.error('请输入手机号！');
          return;
        }
        axios.post(sendCodeUrl, {mobile: this.bindForm.mobile}).then((res) => {
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
      unbind() {
        this.$confirm('确认解绑手机号码?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          axios.post(unbindUrl, {}).then((res) => {
            if (res.data.code > 0) {
              this.msg = res.data.msg;
            } else {
              this.$message.success('解绑成功！');
              this.$store.dispatch('user/getInfo', true)
            }
          }).catch((e) => {
            this.msg = '解绑失败！';
            console.log('error:', e.message);
          });
        });
      },
      bind() {
        axios.post(bindUrl, this.bindForm).then((res) => {
          if (res.data.code > 0) {
            this.msg = res.data.msg;
          } else {
            this.$message.success('绑定成功！');
            this.$store.dispatch('user/getInfo', true)
            this.bindDialog = false
          }
        }).catch((e) => {
          this.msg = '绑定失败！';
          console.log('error:', e.message);
        });
      }
    },
  };
</script>
<style lang="less">
  .user-info-container {
    height: 100%;

    .el-card {
      height: 100%;

      .user-info {
        width: 40%;
        margin: 10% 25%;

        .info-row {
          height: 50px;
          line-height: 50px;

          .info-title {
            text-align: right;
            margin-right: 10px;
          }
        }
      }
    }
  }
</style>

<template>
  <div class="app-container" :style="`height: ` + tableHeight + `px;`">
    <el-card class="user-card">
      <div slot="header">
        <span style="font-weight: bold;">头像</span>
      </div>
      <user-avatar/>
    </el-card>
    <el-card class="user-card">
      <div slot="header">
        <span style="font-weight: bold;">基本信息</span>
      </div>
      <div>
        <el-form ref="form" :model="userInfo" :rules="rules" label-position="left" label-width="300px">
          <el-form-item label="账号:" prop="account">
            <template slot="label">
              <div style="float:left;">
                <svg-icon icon-class="account"/>
              </div>
              <div class="user-item-label">&nbsp;用户账号</div>
              :
            </template>
            <div v-if="showEdit.account">
              <div class="user-item-value">
                <el-input type="input" style="width: 300px;" v-model="userInfo.account"></el-input>
              </div>
              <el-button class="user-item-btn" type="text" style="color: #67C23A;"
                         @click="updateBasicInfo('account')">保存
              </el-button>
              <el-button class="user-item-btn" type="text" style="color: #909399;"
                         @click="cancelBasicInfo('account')">取消
              </el-button>
            </div>
            <div v-else>
              <div class="user-item-value">
                <el-input type="input" readonly style="width: 300px;" v-model="userInfo.account"></el-input>
              </div>
              <el-button class="user-item-btn" type="text" @click="showEdit.account=true">修改</el-button>
            </div>
          </el-form-item>
          <el-divider/>
          <el-form-item label="用户名:" prop="name">
            <template slot="label">
              <div style="float:left;">
                <svg-icon icon-class="people"/>
              </div>
              <div class="user-item-label">&nbsp;用户名</div>
              :
            </template>
            <div v-if="showEdit.name">
              <div class="user-item-value">
                <el-input type="input" style="width: 300px;" v-model="userInfo.name"></el-input>
              </div>
              <el-button class="user-item-btn" type="text" style="color: #67C23A;"
                         @click="updateBasicInfo('name')">保存
              </el-button>
              <el-button class="user-item-btn" type="text" style="color: #909399;"
                         @click="cancelBasicInfo('name')">取消
              </el-button>
            </div>
            <div v-else>
              <div class="user-item-value">
                <el-input type="input" readonly style="width: 300px;" v-model="userInfo.name"></el-input>
              </div>
              <el-button class="user-item-btn" type="text" @click="showEdit.name=true">修改</el-button>
            </div>
          </el-form-item>
          <el-divider/>
          <el-form-item label="手机号:" prop="mobile">
            <template slot="label">
              <div style="float:left;">
                <svg-icon icon-class="mobile"/>
              </div>
              <div class="user-item-label">&nbsp;手机号</div>
              :
            </template>
            <div v-if="showEdit.mobile">
              <div class="user-item-value">
                <el-input type="input" style="width: 300px;" v-model="userInfo.mobile"></el-input>
              </div>
              <el-button class="user-item-btn" type="text" style="color: #67C23A;"
                         @click="updateBasicInfo('mobile')">保存
              </el-button>
              <el-button class="user-item-btn" type="text" style="color: #909399;"
                         @click="cancelBasicInfo('mobile')">取消
              </el-button>
            </div>
            <div v-else>
              <div class="user-item-value">
                <el-input type="input" readonly style="width: 300px;" v-model="userInfo.mobile"></el-input>
              </div>
              <el-button class="user-item-btn" type="text" @click="showEdit.mobile=true">修改</el-button>
            </div>
          </el-form-item>
          <el-divider/>
          <el-form-item label="邮箱:" prop="email">
            <template slot="label">
              <div style="float:left;">
                <svg-icon icon-class="email"/>
              </div>
              <div class="user-item-label">&nbsp;用户邮箱</div>
              :
            </template>
            <div v-if="showEdit.email">
              <div class="user-item-value">
                <el-input type="input" style="width: 300px;" v-model="userInfo.email"></el-input>
              </div>
              <el-button class="user-item-btn" type="text" style="color: #67C23A;"
                         @click="updateBasicInfo('email')">保存
              </el-button>
              <el-button class="user-item-btn" type="text" style="color: #909399;"
                         @click="cancelBasicInfo('email')">取消
              </el-button>
            </div>
            <div v-else>
              <div class="user-item-value">
                <el-input type="input" readonly style="width: 300px;" v-model="userInfo.email"></el-input>
              </div>
              <el-button class="user-item-btn" type="text" @click="showEdit.email=true">修改</el-button>
            </div>
          </el-form-item>
        </el-form>
      </div>
    </el-card>
    <el-card class="user-card">
      <div slot="header">
        <span style="font-weight: bold;">部门</span>
      </div>
      <div v-for="(item, key) in userHasInfo.deptMap" :key="key">
        <el-button type="text" size="mini">
          {{ item.fullName }}
        </el-button>
      </div>
    </el-card>
    <el-card class="user-card">
      <div slot="header">
        <span style="font-weight: bold;">角色</span>
      </div>
      <div v-for="(item, key) in userHasInfo.roleMap" :key="key">
        <el-button type="text" size="mini">
          {{ item.fullName }}
        </el-button>
      </div>
    </el-card>
    <el-card class="user-card">
      <div slot="header">
        <span style="font-weight: bold;">菜单权限</span>
      </div>
      <div>
        <el-tree
          :data="userHasInfo.menuTree"
          :indent="32"
          empty-text="无任何权限"
          node-key="id"
          default-expand-all
          :props="{ expandTrigger: 'hover', label: 'title', value: 'id' }"
        >
          <span slot-scope="{ node, data }" class="custom-tree-node">
            <el-button v-if="data.type === 1" type="text" size="mini" style="color: #909399;">
              {{ node.label }}
              <span style="font-size: 5px;color: gray;">({{ data.menuName }})</span>
            </el-button>
            <el-button v-else-if="data.type === 2" type="text" size="mini" style="color: #67C23A;">
              {{ node.label }}
              <span style="font-size: 5px;color: gray;">({{ data.menuName }})</span>
            </el-button>
            <el-button v-else type="text" size="mini" style="color: #E6A23C;">
              {{ node.label }}
              <span style="font-size: 5px;color: gray;">({{ data.menuName }})</span>
            </el-button>
          </span>
        </el-tree>
      </div>
    </el-card>
  </div>
</template>

<script>
  import {
    getHasInfo,
    updateAccount,
    updateUsername,
    updateMobile,
    updateEmail,
  } from "@/api/user";
  import userAvatar from './user-avatar';
  import store from "@/store";

  export default {
    name: "UserProfileInfo",

    props: {
      tableHeight: {
        type: Number
      }
    },
    components: {
      userAvatar
    },

    data() {
      return {
        showEdit: {
          account: false,
          name: false,
          mobile: false,
          email: false,
        },
        userHasInfo: {
          menuTree: [],
          roleMap: [],
          deptMap: [],
        },
        userInfo: {
          account: '',
          name: '',
          mobile: '',
          email: '',
        },
        // 表单校验
        rules: {
          account: [
            {required: true, message: "账户不能为空", trigger: "change"},
            {min: 6, max: 20, message: "长度在 6 到 20 个字符", trigger: "change"}
          ],
          name: [
            {required: true, message: "用户名不能为空", trigger: "change"},
          ],
        }
      }
    },

    async created() {
      const res = await getHasInfo();
      this.userHasInfo = res?.data || {};
      this.userInfo = {
        account: store.getters.account,
        name: store.getters.name,
        mobile: store.getters.mobile,
        email: store.getters.email,
      }
    },
    methods: {
      updateBasicInfo(type) {
        this.$refs.form.validate(valid => {
          if (valid) {
            let func;
            let commitPath;
            if (type === 'account') {
              func = updateAccount;
              commitPath = 'user/SET_ACCOUNT';
            } else if (type === 'name') {
              func = updateUsername;
              commitPath = 'user/SET_NAME';
            } else if (type === 'mobile') {
              func = updateMobile;
              commitPath = 'user/SET_MOBILE';
            } else {
              func = updateEmail;
              commitPath = 'user/SET_EMAIL';
            }
            func(this.userInfo).then((res) => {
              if (res.code > 0) {
                this.$message.error(res.msg)
              } else {
                this.$message.success('修改成功');
                store.commit(commitPath, this.userInfo[type]);
                this.showEdit[type] = false;
              }
            }).catch((e) => {
              console.log('error submit!')
            })
          } else {
            console.log('error submit!!');
            return false
          }
        });
      },
      cancelBasicInfo(type) {
        this.showEdit[type] = false;
        this.userInfo[type] = store.getters[type];
      }
    }
  }
</script>

<style scoped lang="scss">

  .app-container {
    overflow: auto;

    .user-card {
      width: 80%;
      margin-bottom: 20px;

      .user-item-label {
        float: left;
        width: 80px;
        text-align-last: justify;
      }

      .user-item-value {
        float: left;
        width: 400px;
      }
    }
  }
</style>

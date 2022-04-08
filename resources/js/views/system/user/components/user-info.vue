<template>
  <div class="app-container" :style="`height: ` + tableHeight + `px;`">
    <el-card class="user-card">
      头像
    </el-card>
    <el-card class="user-card">
      <div slot="header">
        <span style="font-weight: bold;">基本信息</span>
      </div>
      <div>
        <el-form label-position="left" label-width="300px">
          <el-form-item label="账号:">
            <template slot="label">
              <div style="float:left;">
                <svg-icon icon-class="account"/>
              </div>
              <div class="user-item-label">&nbsp;用户账号</div>
              :
            </template>
            <div class="user-item-value">{{ account }}</div>
            <el-button class="user-item-btn" type="text">修改</el-button>
          </el-form-item>
          <el-divider/>
          <el-form-item label="用户名:">
            <template slot="label">
              <div style="float:left;">
                <svg-icon icon-class="people"/>
              </div>
              <div class="user-item-label">&nbsp;用户名</div>
              :
            </template>
            <div class="user-item-value">{{ name }}</div>
            <el-button class="user-item-btn" type="text">修改</el-button>
          </el-form-item>
          <el-divider/>
          <el-form-item label="手机号:">
            <template slot="label">
              <div style="float:left;">
                <svg-icon icon-class="mobile"/>
              </div>
              <div class="user-item-label">&nbsp;手机号</div>
              :
            </template>
            <div class="user-item-value">{{ mobile }}</div>
            <el-button class="user-item-btn" type="text">修改</el-button>
          </el-form-item>
          <el-divider/>
          <el-form-item label="邮箱:">
            <template slot="label">
              <div style="float:left;">
                <svg-icon icon-class="email"/>
              </div>
              <div class="user-item-label">&nbsp;用户邮箱</div>
              :
            </template>
            <div class="user-item-value">{{ email }}</div>
            <el-button class="user-item-btn" type="text">修改</el-button>
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
  import {getHasInfo} from "@/api/user";

  export default {
    name: "UserProfileInfo",

    props: {
      tableHeight: {
        type: Number
      },
      account: {
        type: String
      },
      name: {
        type: String
      },
      mobile: {
        type: String
      },
      email: {
        type: String
      },
      avatar: {
        type: String
      },
    },

    data() {
      return {
        userHasInfo: {
          menuTree: [],
          roleMap: [],
          deptMap: [],
        }
      }
    },

    async created() {
      const res = await getHasInfo();
      this.userHasInfo = res?.data || {};
      console.log(this.userHasInfo, 'this.userHasInfo')
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
        width: 300px;
      }

      .user-item-value {
        float: left;
        width: 300px;
      }
    }
  }
</style>

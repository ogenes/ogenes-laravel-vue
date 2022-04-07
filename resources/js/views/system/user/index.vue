<template>
  <div>
    <el-card>
      <div style="float: right">
        <el-popover trigger="click" placement="bottom-start">
          <el-form :inline="true" label-width="60px" label-position="left">
            <el-row>
              <el-form-item label="用户名:">
                <el-input v-model="queryParams.username" @keyup.enter.native="queryList" class="form-item-width"
                          placeholder="支持模糊搜索" clearable/>
              </el-form-item>
              <el-form-item label="账户:">
                <el-input v-model="queryParams.account" @keyup.enter.native="queryList" class="form-item-width"
                          placeholder="支持模糊搜索" clearable/>
              </el-form-item>
              <el-form-item label="手机号:">
                <el-input v-model="queryParams.mobile" @keyup.enter.native="queryList" class="form-item-width"
                          placeholder="支持模糊搜索" clearable/>
              </el-form-item>
            </el-row>
            <el-row>
              <el-form-item label="状态:">
                <el-select v-model="queryParams.userStatus" placeholder="请选择" clearable class="form-item-width">
                  <el-option v-for="(v, k) in USER_STATUS_OPTION" :key="k" :value="v.value" :label="v.label"/>
                </el-select>
              </el-form-item>

              <el-form-item label="部门:" prop="deptIds">
                <el-cascader
                  v-model="queryParams.deptIds"
                  :options="departments"
                  :props="defaultProps"
                  :show-all-levels="false"
                  :collapse-tags="true"
                  placeholder="请选择"
                  filterable
                  clearable
                  class="form-item-width"
                />
              </el-form-item>
              <el-form-item label="角色:" prop="roleIds">
                <el-cascader
                  v-model="queryParams.roleIds"
                  :options="options.roleTree"
                  :props="{
                expandTrigger: 'hover',
                label: 'roleName',
                value: 'id',
                emitPath: false,
                multiple: true,
                checkStrictly: true
              }"
                  :show-all-levels="false"
                  :collapse-tags="true"
                  placeholder="请选择"
                  filterable
                  clearable
                  class="form-item-width"
                />
              </el-form-item>
            </el-row>
            <el-row>
              <el-form-item label=" ">
                <el-button type="primary" @click="queryList">查询</el-button>
                <el-button type="info" @click="clearFilter">清空</el-button>
              </el-form-item>
            </el-row>
          </el-form>

          <el-button slot="reference" type="text" :style="hasFilter ? 'color: #67C23A' : 'color: #909399'"
                     icon="el-icon-search">
            筛选
          </el-button>
        </el-popover>

        <el-button v-permission="[BTN_USER_ADD]" type="text" icon="el-icon-plus" @click="showDialog=true">{{
          BTN_MAP_USER[BTN_USER_ADD]}}
        </el-button>
      </div>
      <div class="page-position">
        <el-pagination
          background
          :page-size="queryParams.pageSize"
          :page-sizes="pageSizes"
          :current-page="queryParams.page"
          layout="total, sizes, prev, pager, next, jumper"
          :total="result.cnt"
          @size-change="handleListSizeChange"
          @current-change="handleListCurrentChange"
        />
      </div>
      <el-table
        :data="result.list"
        v-loading.fullscreen.lock="loading"
        border
        :default-sort="queryParams.sort"
        @sort-change="sortChange"
        :max-height="tableHeight"
      >
        <el-table-column fixed sortable="custom" label="用户ID" prop="uid" width="100px" align="center"/>
        <el-table-column fixed sortable="custom" label="用户名" prop="avatar" width="120px" align="center">
          <template slot-scope="scope">
            <el-popover
              v-if="scope.row.avatar"
              placement="right-start"
              trigger="hover">
              <el-avatar :size="200" fit="cover" :src="scope.row.avatar"/>
              <el-avatar slot="reference" fit="cover" :src="scope.row.avatar"/>
            </el-popover>
            <el-avatar v-else icon="el-icon-user-solid"/>
            <div>
              {{ scope.row.username }}
            </div>
          </template>
        </el-table-column>
        <el-table-column fixed sortable="custom" label="用户账号" prop="account" width="120px" align="center"/>
        <el-table-column sortable="custom" label="手机号" prop="mobile" width="120px" align="center"/>
        <el-table-column sortable="custom" label="邮箱" prop="email" width="200px" align="left"/>
        <el-table-column label="角色" prop="email" width="300px" align="left">
          <template slot-scope="scope">
            <el-tag v-for="(item, key) in scope.row.roles" :key="key" v-if="key < 3" type="info"
                    style="margin-right: 10px;">
              {{ item }}
            </el-tag>
            <el-popover
              v-if="scope.row.roles.length > 3"
              placement="top-end"
              trigger="hover">
              <div v-for="(item, key) in scope.row.roles" :key="key">
                <el-tag type="info">
                  {{ item }}
                </el-tag>
              </div>
              <el-tag slot="reference" type="">
                …
              </el-tag>
            </el-popover>
            <el-button v-permission="[BTN_USER_ROLE]" type="text" icon="el-icon-edit" size="mini"
                       @click="showEditRole(scope.row)">{{
              BTN_MAP_USER[BTN_USER_ROLE] }}
            </el-button>
          </template>
        </el-table-column>
        <el-table-column label="状态" prop="userStatus" width="200px" align="center">
          <template slot-scope="scope">
            <el-switch
              v-model="scope.row.userStatus"
              active-text="启用"
              inactive-text="禁用"
              active-color="#67C23A"
              inactive-color="#F56C6C"
              :disabled="!checkPermission([BTN_USER_STATUS])"
              @change="switchStatus($event, scope.row)"
            >
            </el-switch>
          </template>
        </el-table-column>
        <el-table-column label="部门" prop="departments" width="500px" align="left">
          <template slot-scope="scope">
            <el-tag type="success" v-for="(item, key) in scope.row.departments" :key="key" v-if="key < 1">{{ item }}
            </el-tag>
            <el-popover
              v-if="scope.row.departments.length > 1"
              placement="top-end"
              trigger="hover">
              <div v-for="(item, key) in scope.row.departments" :key="key">
                <el-tag type="success">{{ item }}</el-tag>
              </div>
              <el-tag slot="reference" type="">…</el-tag>
            </el-popover>
          </template>
        </el-table-column>
        <el-table-column sortable="custom" label="最近一次登录时间" prop="lastLoginAt" width="160px" align="center"/>
        <el-table-column sortable="custom" label="最近一次登录地" prop="lastLoginIp" width="150px" align="center"/>
        <el-table-column sortable="custom" label="最近一次修改时间" prop="updatedAt" width="160px" align="center"/>
        <el-table-column fixed="right" label="操作" align="center" width="220px">
          <template slot-scope="scope">
            <div>
              <el-button v-permission="[BTN_USER_EDIT]" type="text" icon="el-icon-edit" @click="showEdit(scope.row)">
                {{ BTN_MAP_USER[BTN_USER_EDIT] }}
              </el-button>
              <el-button v-permission="[BTN_USER_RESET]" type="text" icon="el-icon-refresh"
                         style="color:#F56C6C;" :disabled="scope.row.uid === 1"
                         @click="resetPass(scope.row)">
                {{ BTN_MAP_USER[BTN_USER_RESET] }}
              </el-button>
            </div>
          </template>
        </el-table-column>
      </el-table>
    </el-card>

    <el-drawer
      v-if="showDialog"
      :title="dialogTitle"
      direction="rtl"
      size="30%"
      :visible.sync="showDialog"
      :wrapper-closable="false"
      style="padding-left: 20px"
      custom-class="overflow-auto"
    >
      <user-form
        :departments="departments"
        :default-props="defaultProps"
        :user-params="userParams"
        :close-dialog="closeDialog"
      />
    </el-drawer>

    <el-dialog
      title="编辑角色"
      :visible.sync="showRoleDialog"
      :destroy-on-close="true"
      :close-on-click-modal="false"
      :before-close="closeDialog"
    >
      <user-role
        :role-tree="options.roleTree"
        :user-has-role-params="userHasRoleParams"
        :close-dialog="closeDialog"
      />
    </el-dialog>
  </div>
</template>

<script>
  import {
    getDepartmentList,
    getRoleTree,
    getList,
    switchStatus,
    resetPassByUid,
    USER_STATUS_OPTION
  } from '@/api/user';

  import userForm from "./components/user-form";
  import userRole from "./components/user-role";
  import checkPermission from "@/utils/permission";

  import {
    BTN_MAP_USER,
    BTN_USER_ADD,
    BTN_USER_EDIT,
    BTN_USER_RESET,
    BTN_USER_STATUS,
    BTN_USER_ROLE,
  } from "../../../api/btn";

  export default {
    name: "UserManage",

    components: {
      userForm,
      userRole,
    },
    mounted() {
      this.$nextTick(() => {
        this.tableHeight = window.innerHeight - 165;
      })
    },
    data() {
      return {
        USER_STATUS_OPTION,
        BTN_MAP_USER,
        BTN_USER_ADD,
        BTN_USER_EDIT,
        BTN_USER_RESET,
        BTN_USER_STATUS,
        BTN_USER_ROLE,
        checkPermission,

        loading: false,
        tableHeight: 0,
        showSearch: true,
        options: {
          roleTree: []
        },

        departments: [],
        defaultProps: {
          expandTrigger: 'hover',
          label: 'name',
          value: 'id',
          emitPath: false,
          multiple: true,
          checkStrictly: true
        },

        queryParams: {
          page: 1,
          pageSize: 20,
          username: '',
          account: '',
          mobile: '',
          userStatus: '',
          roleIds: [],
          deptIds: [],
          sort: {
            prop: 'uid',
            order: 'descending',
          }
        },
        pageSizes: [10, 20, 50, 100, 200],
        result: {
          list: [],
          cnt: 0
        },

        showDialog: false,
        dialogTitle: '添加用户',
        userParams: {
          id: 0,
          username: '',
          mobile: '',
          email: '',
          avatar: '',
          deptIds: [],
        },

        showRoleDialog: false,
        userHasRoleParams: {
          uid: 0,
          roleIds: [],
        },
      }
    },

    computed: {
      hasFilter() {
        return !!(this.queryParams.username
          || this.queryParams.account
          || this.queryParams.mobile
          || [0, 1].includes(this.queryParams.userStatus)
          || this.queryParams.account
          || this.queryParams.roleIds.length > 0
          || this.queryParams.deptIds.length > 0);

      },
    },

    async created() {
      const ret = await getDepartmentList();
      this.departments = ret?.data || [];

      const roleTreeRet = await getRoleTree();
      this.options.roleTree = roleTreeRet?.data || [];
      await this.queryList();
    },

    methods: {
      async queryList() {
        this.queryParams.page = 1;
        this.getList();
      },

      getList() {
        this.loading = true;
        getList(this.queryParams).then((res) => {
          if (res.code > 0) {
            this.$message.error(res.msg)
          } else {
            this.queryParams.page = parseInt(res.data.page);
            this.queryParams.pageSize = parseInt(res.data.pageSize);
            this.result.cnt = parseInt(res.data.cnt);
            this.result.list = res.data.list;
            this.result.list.forEach(item => {
              item.userStatus = item.userStatus > 0
            })
          }
          this.loading = false
        }).catch((e) => {
          this.loading = false
        })
      },

      handleListSizeChange: function (val) {
        this.queryParams.page = 1;
        this.queryParams.pageSize = val;
        this.getList();
      },
      handleListCurrentChange: function (currentPage) {
        this.queryParams.page = currentPage;
        this.getList()
      },

      sortChange(col) {
        this.queryParams.sort = {
          prop: col.prop,
          order: col.order
        };
        this.queryList();
      },

      clearFilter() {
        this.queryParams = {
          page: 1,
          pageSize: 20,
          username: '',
          account: '',
          mobile: '',
          userStatus: '',
          roleIds: [],
          deptIds: [],
          sort: {
            prop: 'uid',
            order: 'descending',
          }
        };
        this.queryList();
      },

      showEdit(row) {
        this.userParams = {
          id: row.uid,
          username: row.username,
          mobile: row.mobile,
          email: row.email,
          avatar: row.avatar,
          deptIds: row.deptIdArr,
        };
        this.dialogTitle = '修改用户';
        this.showDialog = true;
      },

      showEditRole(row) {
        this.userHasRoleParams = {
          uid: row.uid,
          roleIds: row.roleIdArr || [],
        };
        this.showRoleDialog = true;
      },

      closeDialog() {
        this.showDialog = false;
        this.userParams = {};
        this.dialogTitle = '添加用户';
        this.showRoleDialog = false;
        this.userHasRoleParams = {};
        this.queryList();
      },

      switchStatus($event, row) {
        const label = $event ? '启用' : '禁用';
        row.userStatus = !row.userStatus;
        this.$confirm('确认' + label + '用户?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          this.loading = true;
          switchStatus({id: row.uid, userStatus: $event}).then((res) => {
            if (res.code > 0) {
              this.$message.error(res.msg)
            } else {
              this.$message.success('操作成功');
              this.queryList();
            }
            this.loading = false
          }).catch((e) => {
            this.loading = false
          });
          this.loading = false;
        }).catch(() => {
          this.$message({
            type: 'info',
            message: '已取消'
          });
        });
      },

      resetPass(row) {
        this.$confirm('确认重置用户密码?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          this.loading = true;
          resetPassByUid({id: row.uid}).then((res) => {
            if (res.code > 0) {
              this.$message.error(res.msg)
            } else {
              this.$message.success('操作成功');
              this.$alert(row.username + '的新密码是 ' + res.data.password, '密码重置', {
                confirmButtonText: '确定',
              });
            }
            this.loading = false
          }).catch((e) => {
            this.loading = false
          });
          this.loading = false;
        }).catch(() => {
          this.$message({
            type: 'info',
            message: '已取消'
          });
        });
      }
    }
  }
</script>

<style scoped lang="scss">
  .app-container {
    .form-item-width {
      width: 240px
    }
  }
</style>

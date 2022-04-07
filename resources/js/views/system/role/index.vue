<template>
  <div>
    <el-card>
      <div style="float: right">
        <el-popover trigger="click" placement="bottom-start">
          <el-form :inline="true" label-width="100px" label-position="right">
            <el-row>
              <el-form-item label="角色名:">
                <el-input v-model="queryParams.roleName" clearable @keyup.enter.native="queryList"
                          class="form-item-width"
                          placeholder="支持模糊搜索"/>
              </el-form-item>
              <el-form-item label="状态:">
                <el-select v-model="queryParams.roleStatus" placeholder="请选择！" clearable class="form-item-width">
                  <el-option v-for="(v, k) in ROLE_STATUS_OPTION" :key="k" :value="v.value" :label="v.label"/>
                </el-select>
              </el-form-item>
              <el-form-item label="菜单权限：" prop="menuId">
                <el-cascader
                  v-model="queryParams.menuIds"
                  :options="menuOptions"
                  :props="{
                expandTrigger: 'hover',
                label: 'title',
                value: 'id',
                checkStrictly: true,
                emitPath: false,
                multiple: true
              }"
                  placeholder="请选择"
                  filterable
                  clearable
                  :collapse-tags="true"
                  class="form-item-width"
                />
              </el-form-item>
            </el-row>
            <el-row>
              <el-form-item label="上级角色:" prop="parentId">
                <el-cascader
                  v-model="queryParams.parentIds"
                  :options="selectOptions"
                  :filterable="true"
                  :collapse-tags="true"
                  :props="{...defaultProps, multiple: true}"
                  placeholder="请选择！"
                  filterable
                  clearable
                  class="form-item-width"
                />
              </el-form-item>
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
        <el-button v-permission="[BTN_ROLE_ADD]" type="text" icon="el-icon-plus" @click="showDialog=true">
          {{ BTN_MAP_ROLE[BTN_ROLE_ADD] }}
        </el-button>
        <!--        <el-button type="text" icon="el-icon-download">导出</el-button>-->
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
        <el-table-column type="" prop="id" sortable="custom" width="100" align="center" label="角色ID"/>
        <el-table-column prop="roleName" sortable="custom" width="150" align="left" label="角色名"/>
        <el-table-column prop="parentId" sortable="custom" width="100" align="center" label="上级ID"/>
        <el-table-column prop="parent" width="200" align="left" label="上级角色"/>
        <el-table-column prop="parent" width="400" align="left" label="菜单权限">
          <template slot="header" slot-scope="scope">
            <el-select v-model="menuSystemId" size="mini" style="width: 200px">
              <el-option v-for="(v, k) in options.system" :key="k" :value="k" :label="v"/>
            </el-select>
            <span>菜单权限</span>
          </template>
          <template slot-scope="scope">
            <div v-for="(item, key) in scope.row.system" :key="key" v-if="item.systemId === parseInt(menuSystemId)">
              <div style="float: left;">
                <el-tree
                  style="width: 300px; border-radius: 5px;"
                  :data="item.menuTree"
                  :indent="32"
                  empty-text="无任何权限"
                  node-key="id"
                  :default-checked-keys="item.menuIds"
                  :props="{ expandTrigger: 'hover', label: 'title', value: 'id' }"
                >
                  <span slot-scope="{ node, data }" class="custom-tree-node">
                    <span>{{ node.label }} <span style="font-size: 5px;color: gray;">({{ data.menuName }})</span></span>
                  </span>
                </el-tree>
              </div>
              <div style="float:left;">
                <el-button v-permission="[BTN_ROLE_MENU]" type="text" size="mini" icon="el-icon-edit"
                           @click="showRoleHasMenu(scope.row, item.menuIds)">
                  {{ BTN_MAP_ROLE[BTN_ROLE_MENU] }}
                </el-button>
              </div>
            </div>
          </template>
        </el-table-column>
        <!--        <el-table-column prop="parent" width="400" align="left" label="数据权限">-->
        <!--          <template slot="header" slot-scope="scope">-->
        <!--            <el-select v-model="dataSystemId" size="mini" style="width: 200px">-->
        <!--              <el-option v-for="(v, k) in options.system" :key="k" :value="k" :label="v"/>-->
        <!--            </el-select>-->
        <!--            <span>数据权限</span>-->
        <!--          </template>-->
        <!--          <template slot-scope="scope">-->
        <!--            <div style="float: left;">-->
        <!--            </div>-->
        <!--            <div style="float: left;">-->
        <!--              <el-button type="text" @click="showRoleHasData(scope.row)">编辑</el-button>-->
        <!--            </div>-->
        <!--          </template>-->
        <!--        </el-table-column>-->
        <el-table-column prop="createdAt" sortable="custom" width="160" align="center" label="创建时间"/>
        <el-table-column prop="updatedAt" sortable="custom" width="160" align="center" label="更新时间"/>
        <el-table-column fixed="right" label="状态" prop="roleStatus" width="200px" align="center">
          <template slot="header">
            <span>状态</span>
            <el-popover trigger="hover">
              <div>
                <h2><p style="color: red;"><b>说明：</b></p></h2>
                <p>1. 角色禁用状态时，用户不能被授予该角色!</p>
                <p>2. 角色禁用状态时，已拥有该角色的用户对应的权限失效!</p>
              </div>
              <i class="el-icon-question" slot="reference"></i>
            </el-popover>
          </template>
          <template slot-scope="scope">
            <el-switch
              v-model="scope.row.roleStatus"
              active-text="启用"
              inactive-text="禁用"
              active-color="#67C23A"
              inactive-color="#F56C6C"
              :disabled="!checkPermission([BTN_ROLE_STATUS])"
              @change="switchStatus($event, scope.row)"
            >
            </el-switch>
          </template>
        </el-table-column>
        <el-table-column fixed="right" label="操作" width="200" align="center">
          <template slot-scope="scope">
            <el-button v-permission="[BTN_ROLE_EDIT]" type="text" icon="el-icon-edit" @click="showEdit(scope.row)">
              {{ BTN_MAP_ROLE[BTN_ROLE_EDIT] }}
            </el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>

    <el-dialog
      :title="dialogTitle"
      :visible.sync="showDialog"
      :destroy-on-close="true"
      :close-on-click-modal="false"
      :before-close="closeDialog"
    >
      <role-form
        :close-dialog="closeDialog"
        :role-params="roleParams"
        :default-props="defaultProps"
        :role-tree="options.roleTree"
        :select-options="selectOptions"
      />
    </el-dialog>

    <el-dialog
      title="菜单权限"
      :visible.sync="showRoleHasMenuDialog"
      :destroy-on-close="true"
      :close-on-click-modal="false"
      :before-close="closeDialog"
    >
      <menu-form
        :close-dialog="closeDialog"
        :role-has-menu-params="roleHasMenuParams"
        :menu-tree="options.menuTree"
      />
    </el-dialog>
  </div>
</template>

<script>
  import {
    getOptions,
    getList,
    getMenuTree,
    getDataTree,
    getRoleTree,
    switchStatus,
    ROLE_STATUS_OPTION
  } from '@/api/system/role';
  import {
    BTN_MAP_ROLE,
    BTN_ROLE_ADD,
    BTN_ROLE_EDIT,
    BTN_ROLE_MENU,
    BTN_ROLE_STATUS
  } from "@/api/btn";
  import {deepClone} from "@/utils";
  import checkPermission from "@/utils/permission";

  import roleForm from "./components/role-form";
  import menuForm from "./components/menu-form";


  export default {
    name: "RoleManage",

    components: {
      roleForm,
      menuForm
    },
    mounted() {
      this.$nextTick(() => {
        this.tableHeight = window.innerHeight - 165;
      })
    },
    data() {
      return {
        ROLE_STATUS_OPTION,
        BTN_MAP_ROLE,
        BTN_ROLE_ADD,
        BTN_ROLE_EDIT,
        BTN_ROLE_MENU,
        BTN_ROLE_STATUS,
        checkPermission,

        loading: false,
        tableHeight: 0,
        showSearch: true,
        isExpansion: true,
        options: {
          system: [],
          menuTree: [],
          dataTree: [],
          roleTree: [],
        },
        menuOptions: [],

        queryParams: {
          roleName: '',
          roleStatus: '',
          parentIds: [],
          menuIds: [],
          sort: {
            prop: 'id',
            order: 'ascending',
          },
          page: 1,
          pageSize: 20,
        },
        pageSizes: [10, 20, 50, 100, 200],
        result: {
          list: [],
          cnt: 0
        },

        defaultProps: {
          expandTrigger: 'hover',
          label: 'roleName',
          value: 'id',
          emitPath: false,
          checkStrictly: true
        },

        showDialog: false,
        dialogTitle: '添加角色',
        roleParams: {
          id: 0,
          parentId: '',
          roleName: '',
          desc: '',
        },

        menuSystemId: '1',
        dataSystemId: '1',
        roleHasMenuParams: {
          roleId: 0,
          systemId: 1,
          menuIds: []
        },
        showRoleHasMenuDialog: false,
        roleHasDataParams: {
          roleId: 0,
          dataIds: []
        },
        showRoleHasDataDialog: false,
      }
    },

    computed: {
      selectOptions() {
        function dealDisabled(id, data, disabled) {
          if (data?.id === id) {
            disabled = true;
          }
          if (typeof data === 'object') {
            data.disabled = disabled;
            const children = data?.children || [];
            if (children) {
              children.forEach(item => {
                dealDisabled(id, item, disabled)
              })
            }
          }
        }

        const selectedId = this.roleParams?.id || 0;
        const tmpData = deepClone(this.options.roleTree || []);
        tmpData.forEach(item => {
          dealDisabled(selectedId, item, false);
        });
        return tmpData;
      },
      hasFilter() {
        return !!(this.queryParams.roleName
          || [0, 1].includes(this.queryParams.roleStatus)
          || this.queryParams.account
          || this.queryParams.parentIds.length > 0
          || this.queryParams.menuIds.length > 0);

      },
    },

    async created() {
      await this.initOptions();
      await this.initRoleTree();
      await this.queryList();
    },

    methods: {
      async initOptions() {
        const ret = await getOptions();
        this.options.system = ret?.data?.system || [];

        const menuTreeRet = await getMenuTree();
        this.options.menuTree = menuTreeRet?.data || {};
        for (const i in this.options.menuTree) {
          this.menuOptions.push({
            disabled: true,
            id: this.options.menuTree[i]?.systemId || 0,
            title: this.options.menuTree[i]?.systemName || '',
            children: this.options.menuTree[i]?.menu || []
          });
        }

        const dataTreeRet = await getDataTree();
        this.options.dataTree = dataTreeRet?.data || [];

      },
      async initRoleTree() {
        const roleTreeRet = await getRoleTree();
        this.options.roleTree = roleTreeRet?.data || [];

        this.roleParams = {};
      },
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
              item.roleStatus = item.roleStatus > 0;
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
        this.getList();
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
          roleName: '',
          roleStatus: '',
          parentIds: [],
          menuIds: [],
          sort: {
            prop: 'id',
            order: 'ascending',
          },
          page: 1,
          pageSize: 20,
        };
        this.queryList();
      },

      showEdit(row) {
        this.roleParams = {
          id: row.id,
          parentId: row.parentId,
          roleName: row.roleName,
          desc: row.desc,
        };
        this.dialogTitle = '修改角色';
        this.showDialog = true;
      },

      async showRoleHasMenu(row, menuIds) {
        this.roleHasMenuParams = {
          roleId: row.id,
          systemId: this.menuSystemId,
          menuIds: menuIds,
        };
        this.showRoleHasMenuDialog = true;
      },
      showRoleHasData(row) {
        this.roleHasDataParams = {
          roleId: row.id,
          dataIds: row.dataIds,
        };
        this.showRoleHasMenuDialog = true;
      },

      closeDialog() {
        this.showDialog = false;
        this.showRoleHasMenuDialog = false;
        this.showRoleHasDataDialog = false;
        this.roleParams = {};
        this.roleHasMenuParams = {};
        this.roleHasDataParams = {};
        this.dialogTitle = '添加角色';
        this.queryList();
        this.initRoleTree();
      },

      switchStatus($event, row) {
        const label = $event ? '启用' : '禁用';
        row.roleStatus = !row.roleStatus;
        this.$confirm('确认' + label + '角色?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          this.loading = true;
          switchStatus({roleId: row.id, roleStatus: $event}).then((res) => {
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
    }
  }
</script>

<style scoped lang="scss">
  .app-container {
    .form-item-width {
      width: 300px
    }
  }
</style>

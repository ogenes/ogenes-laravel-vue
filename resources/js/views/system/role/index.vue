<template>
  <div class="app-container">
    <el-card>
      <div slot="header">
        <span>查询条件</span>
      </div>
      <el-form :inline="true">
        <el-row>
          <el-form-item label="角色名:">
            <el-input v-model="queryParams.roleName" clearable @keyup.enter.native="queryList" class="form-item-width"
                      placeholder="支持模糊搜索"/>
          </el-form-item>
          <el-form-item label="状态:">
            <el-select v-model="queryParams.roleStatus" placeholder="请选择！" clearable class="form-item-width">
              <el-option v-for="(v, k) in ROLE_STATUS_OPTION" :key="k" :value="v.value" :label="v.label"/>
            </el-select>
          </el-form-item>
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
            <el-button type="primary" icon="el-icon-plus" @click="showDialog=true">新增</el-button>
          </el-form-item>
        </el-row>
      </el-form>
    </el-card>
    <el-card>
      <div slot="header">
        <span>查询结果</span>
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
        border
        height="600px"
      >
        <el-table-column type="" fixed prop="id" width="100" align="center" label="角色ID"/>
        <el-table-column fixed prop="roleName" width="150" align="left" label="角色名"/>
        <el-table-column prop="parentId" width="100" align="center" label="上级ID"/>
        <el-table-column prop="parent" width="200" align="left" label="上级角色"/>
        <el-table-column label="状态" prop="roleStatus" width="200px" align="center">
          <template slot-scope="scope">
            <el-switch
              v-model="scope.row.roleStatus"
              active-text="启用"
              inactive-text="禁用"
              active-color="#67C23A"
              inactive-color="#F56C6C"
              @change="switchStatus($event, scope.row)"
            >
            </el-switch>
          </template>
        </el-table-column>
        <el-table-column prop="parent" width="200" align="left" label="菜单权限"/>
        <el-table-column prop="parent" width="200" align="left" label="数据权限"/>
        <el-table-column prop="createdAt" width="160" align="center" label="创建时间"/>
        <el-table-column prop="updatedAt" width="160" align="center" label="更新时间"/>
        <el-table-column fixed="right" label="操作" width="200" align="center">
          <template slot-scope="scope">
            <el-button type="primary" @click="showEdit(scope.row)">编辑</el-button>
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
  </div>
</template>

<script>
  import {
    getList,
    getRoleTree,
    switchStatus,
    ROLE_STATUS_OPTION
  } from '@/api/system/role';
  import {deepClone} from "@/utils";

  import roleForm from "./components/role-form";


  export default {
    name: "RoleManage",

    components: {
      roleForm
    },

    data() {
      return {
        ROLE_STATUS_OPTION,

        loading: false,
        isExpansion: true,
        options: {
          menuTree: [],
          dataTree: [],
          roleTree: [],
        },

        queryParams: {
          roleName: '',
          roleStatus: '',
          parentIds: [],
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
      }
    },

    async created() {
      await this.initOptions();
      await this.queryList();
    },

    methods: {
      async initOptions() {
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

      closeDialog() {
        this.showDialog = false;
        this.roleParams = {};
        this.dialogTitle = '添加角色';
        this.queryList();
        this.initOptions();
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

    .page-position {
      text-align: right;
      margin: 10px 0;
    }

    .span-color {
      color: #1482f0
    }
  }
</style>

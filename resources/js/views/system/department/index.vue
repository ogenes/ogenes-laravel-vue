<template>
  <div>
    <el-card>
      <div>
        <el-input
          placeholder="输入部门进行搜索"
          v-model="filterText"
          clearable
          style="width: 100%;"
        >
        </el-input>
        <el-table
          ref="departmentTree"
          :data="treeData"
          row-key="id"
          :tree-props="{children: 'children', hasChildren: 'hasChildren'}"
          default-expand-all
          :row-class-name="departmentRowClassName"
          :cell-class-name="departmentColClassName"
        >
          <el-table-column fixed prop="name" width="400" label="部门">
            <template slot="header" slot-scope="scope">
              <span>部门 </span>
              <el-button type="text" style="margin-left: 20px;"
                         :icon="isExpansion ? 'el-icon-folder-remove' : 'el-icon-folder-add'"
                         @click="toggleRowExpansion">
                全部{{ isExpansion ? "收缩" : "展开" }}
              </el-button>
            </template>
          </el-table-column>
          <el-table-column prop="id" width="150" align="center" label="部门ID"/>
          <el-table-column prop="parent" width="500" label="上级部门"/>
          <el-table-column prop="parentId" width="150" align="center" label="上级部门ID"/>
          <el-table-column prop="cnt" width="150" align="center" label="部门人数">
            <template slot-scope="scope">
              <el-button type="text" :disabled="!checkPermission([BTN_DEPT_USER])" @click="showUsers(scope.row)">{{
                scope.row.cnt }}
              </el-button>
            </template>
          </el-table-column>
          <el-table-column fixed="right" width="200" label="操作">
            <template slot="header" slot-scope="scope">
              <span>操作 </span>
              <el-button
                v-permission="[BTN_DEPT_ADD]"
                type="text"
                class="el-icon-plus"
                style="margin-left: 20px;"
                @click="showDialog=true"
              >
                {{BTN_MAP_DEPT[BTN_DEPT_ADD]}}
              </el-button>
            </template>
            <template slot-scope="scope">
              <el-button v-permission="[BTN_DEPT_EDIT]" type="text" icon="el-icon-edit" @click="showEdit(scope.row)">{{
                BTN_MAP_DEPT[BTN_DEPT_EDIT] }}
              </el-button>
              <el-button v-if="scope.row.id > 1" v-permission="[BTN_DEPT_DEL]" type="text" icon="el-icon-delete"
                         style="color:#F56C6C;" @click="remove(scope.row.id)">
                {{ BTN_MAP_DEPT[BTN_DEPT_DEL] }}
              </el-button>
            </template>
          </el-table-column>
        </el-table>
      </div>

    </el-card>

    <el-dialog :visible.sync="showHasUsers" :destroy-on-close="true" :show-close="false">
      <el-button v-for="(item, key) in hasUsers" :key="key">
        <span style="color: #409EFF">{{item.username}}</span>
        <span style="color: #909399">({{item.account}})</span>
      </el-button>
    </el-dialog>
    <!--编辑弹窗-->
    <el-dialog
      :title="dialogTitle"
      :visible.sync="showDialog"
      :destroy-on-close="true"
      :close-on-click-modal="false"
      :before-close="closeDialog"
    >
      <el-form
        ref="departmentParams"
        v-loading="loading"
        :rules="departmentRules"
        :model="departmentParams"
        label-width="120px"
        label-position="top"
      >
        <el-form-item label="上级部门：" prop="parentId">
          <el-cascader
            v-model="departmentParams.parentId"
            :options="selectOptions"
            :props="defaultProps"
            :disabled="departmentParams.id === 1"
            placeholder="请选择上级部门"
            filterable
            clearable
            style="width: 100%"
          />
        </el-form-item>
        <el-form-item label="部门：" prop="name">
          <el-input v-model="departmentParams.name" placeholder="请输入部门名字" clearable style="width: 100%"/>
        </el-form-item>
      </el-form>
      <div slot="footer">
        <el-button type="primary" @click="save"> {{departmentParams.id > 0 ? '编辑' : '新增'}}</el-button>
        <el-button type="info" @click="closeDialog">取消</el-button>
      </div>
    </el-dialog>

  </div>
</template>

<script>
  import {getList, getDepartmentHasUser, add, edit, remove} from '@/api/system/department';
  import {
    BTN_MAP_DEPT,
    BTN_DEPT_ADD,
    BTN_DEPT_EDIT,
    BTN_DEPT_DEL,
    BTN_DEPT_USER,
  } from "@/api/btn";
  import {deepClone} from "@/utils";
  import checkPermission from "@/utils/permission";

  export default {
    name: "DepartmentManage",

    data() {
      return {
        BTN_MAP_DEPT,
        BTN_DEPT_ADD,
        BTN_DEPT_EDIT,
        BTN_DEPT_DEL,
        BTN_DEPT_USER,
        checkPermission,

        loading: false,
        isExpansion: true,
        showHasUsers: false,
        hasUsers: [],

        filterText: '',
        showDialog: false,
        dialogTitle: '添加部门',
        departmentParams: {
          id: 0,
          name: '',
          parentId: ''
        },
        departmentRules: {
          name: [{required: true, message: '请输入部门名字', trigger: 'change'}],
          parentId: [{required: true, message: '请选择上级部门', trigger: 'change'}]
        },

        defaultProps: {
          expandTrigger: 'hover',
          label: 'name',
          value: 'id',
          emitPath: false,
          checkStrictly: true
        },

        list: [],
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

        const selectedId = this.departmentParams?.id || 0;
        dealDisabled(selectedId, this.list[0], false);
        return this.list;
      },
      treeData() {
        if (this.filterText) {
          const treeData = deepClone(this.list);
          return this.handleSearch(treeData, this.filterText)
        }
        return this.list;
      }
    },

    async created() {
      await this.queryList();
    },

    methods: {
      async queryList() {
        this.loading = true;
        const ret = await getList();
        this.list = ret?.data || [];
        this.loading = false;
      },
      async showUsers(row) {
        const ret = await getDepartmentHasUser({id: row.id});
        this.hasUsers = ret?.data || [];
        console.log(this.hasUsers, 'this.hasUsers');
        this.showHasUsers = true;
      },

      handleSearch(treeData, searchValue) {
        if (!treeData || treeData.length === 0) {
          return [];
        }
        const array = [];
        for (let item of treeData) {
          let match = false;
          for (let pro in item) {
            if (typeof (item[pro]) == 'string') {
              match |= item[pro].includes(searchValue);
              if (match) break;
            }
          }
          if (this.handleSearch(item.children, searchValue).length > 0 || match) {
            array.push({
              ...item,
              children: this.handleSearch(item.children, searchValue),
            });
          }
        }
        return array;
      },

      async remove(id) {
        this.$confirm('此操作将永久删除该部门, 是否继续?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          this.loading = true;
          remove({id}).then((res) => {
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
            message: '已取消删除'
          });
        });
      },
      async save() {
        this.$refs.departmentParams.validate(valid => {
          if (valid) {
            this.loading = true;
            const func = this.departmentParams.id > 0 ? edit : add;
            func(this.departmentParams).then((res) => {
              if (res.code > 0) {
                this.$message.error(res.msg)
              } else {
                this.$message.success('操作成功');
                this.closeDialog();
                this.queryList();
              }
              this.loading = false
            }).catch((e) => {
              this.loading = false
            })
          } else {
            console.log('error submit!!');
            return false
          }
        });
      },

      showEdit(row) {
        this.departmentParams = {
          id: row.id,
          name: row.name,
          parentId: row.parentId,
        };
        this.dialogTitle = '编辑';
        this.showDialog = true;
      },

      closeDialog() {
        this.showDialog = false;
        this.dialogTitle = '添加部门';
        this.departmentParams = {};
      },

      departmentRowClassName({row, rowIndex}) {
        if (this.filterText && row.name.indexOf(this.filterText) !== -1) {
          return 'search-row';
        }
        return '';
      },
      departmentColClassName({row, column}) {
        if (column.property === 'name') {
          return 'search-col';
        }
        return '';
      },

      toggleRowExpansion() {
        this.isExpansion = !this.isExpansion;
        this.toggleRowExpansionAll(this.list, this.isExpansion);
      },
      toggleRowExpansionAll(data, isExpansion) {
        data.forEach((item) => {
          this.$refs.departmentTree.toggleRowExpansion(item, isExpansion);
          if (item.children !== undefined && item.children !== null) {
            this.toggleRowExpansionAll(item.children, isExpansion);
          }
        });
      },
    }
  }
</script>

<style>
  .el-table .search-row .search-col {
    color: #F56C6C !important;
  }

</style>
<style scoped>

</style>

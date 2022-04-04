<template>
  <div class="app-container">
    <!--表单-->
    <el-card>
      <el-input
        placeholder="输入菜单|Name|权限标识进行搜索"
        v-model="filterText"
        clearable
        style="width: 100%;"
      >
      </el-input>
      <el-table
        ref="menuTree"
        :data="treeData"
        row-key="id"
        :tree-props="{children: 'children', hasChildren: 'hasChildren'}"
        default-expand-all
        :row-class-name="menuRowClassName"
      >
        <el-table-column type="" fixed prop="id" width="100" align="center" label="菜单ID"/>
        <el-table-column fixed prop="title" width="200" align="left" label="菜单">
          <template slot="header" slot-scope="scope">
            <span>菜单 </span>
            <el-button type="text" @click="toggleRowExpansion">
              全部{{ isExpansion ? "收缩" : "展开" }}
            </el-button>
          </template>
        </el-table-column>
        <el-table-column fixed prop="menuName" label="Name" width="300"/>
        <el-table-column prop="类型" width="150" align="center" label="type">
          <template slot-scope="scope">
            <el-tag :type="`${(scope.row.type === 1 && 'info') ||
                  (scope.row.type === 2 && 'success') ||
                  (scope.row.type === 3 && 'warning')}`">
              {{ menuTypeOption[scope.row.type] }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="parentId" width="150" align="center" label="上级ID"/>
        <el-table-column prop="icon" width="150" align="center" label="图标">
          <template slot-scope="scope">
            <svg-icon :icon-class="scope.row.icon"/>
          </template>
        </el-table-column>
        <el-table-column prop="roles" width="300" align="left" label="权限标识"/>
        <el-table-column prop="createdAt" width="160" align="center" label="创建时间"/>
        <el-table-column prop="updatedAt" width="160" align="center" label="更新时间"/>
        <el-table-column fixed="right" label="操作" width="200" align="center">
          <template slot="header" slot-scope="scope">
            <span>操作 </span>
            <el-button
              v-permission="[BTN_MENU_ADD]"
              type="primary"
              class="el-icon-plus"
              @click="showDialog=true"
            >
              {{ BTN_MAP_MENU[BTN_MENU_ADD] }}
            </el-button>
          </template>
          <template slot-scope="scope">
            <el-button v-permission="[BTN_MENU_EDIT]" type="primary" @click="showEdit(scope.row)">
              {{ BTN_MAP_MENU[BTN_MENU_EDIT] }}
            </el-button>
            <el-button v-permission="[BTN_MENU_DEL]" :disabled="scope.row.children !== undefined" type="danger" @click="remove(scope.row.id)">
              {{ BTN_MAP_MENU[BTN_MENU_DEL] }}
            </el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>

    <!--编辑弹窗-->
    <el-dialog
      :title="dialogTitle"
      :visible.sync="showDialog"
      :destroy-on-close="true"
      :close-on-click-modal="false"
      :before-close="closeDialog"
    >
      <el-form ref="menuParams" v-loading="loading" :rules="menuRules" :model="menuParams"
               label-width="120px" label-position="right">
        <el-form-item label="System：" prop="systemId">
          <el-input v-model="system" placeholder="请输入" readonly style="width: 100%"/>
        </el-form-item>
        <el-form-item label="Name：" prop="menuName">
          <template slot="label">
            <span>Name</span>
            <el-popover trigger="hover">
              <div>
                此内容请跟开发人员确认，需要与代码中保持一致！
              </div>
              <i class="el-icon-question" slot="reference"></i>
            </el-popover>
            <span>：</span>
          </template>
          <el-input v-model="menuParams.menuName" placeholder="Name 需与代码中保持一致！" clearable style="width: 100%"/>
        </el-form-item>
        <el-form-item label="上级菜单：" prop="parentId">
          <template slot="label">
            <span>上级菜单</span>
            <el-popover trigger="hover">
              <div>
                此内容请跟开发人员确认，需要与代码中保持一致！
              </div>
              <i class="el-icon-question" slot="reference"></i>
            </el-popover>
            <span>：</span>
          </template>
          <el-cascader
            v-model="menuParams.parentId"
            :options="selectOptions"
            :props="defaultProps"
            placeholder="上级菜单 需与代码中保持一致！"
            filterable
            clearable
            style="width: 100%"
          />
        </el-form-item>
        <el-form-item label="类型：" prop="type">
          <el-select v-model="menuParams.type" placeholder="请选择" style="width: 100%">
            <el-option
              v-for="(item, key) in menuTypeOption"
              :key="key"
              :label="item"
              :value="key">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="菜单名称：" prop="title">
          <el-input v-model="menuParams.title" placeholder="请输入" clearable style="width: 100%"/>
        </el-form-item>
        <el-form-item label="权限标识：" prop="roles">
          <el-select v-if="menuParams.type === '3'" v-model="menuParams.roles" placeholder="请选择" style="width: 100%">
            <el-option
              v-for="(item, key) in BTN_MAP"
              :key="key"
              :label="key"
              :value="key">
            </el-option>
          </el-select>
          <el-input
            v-else
            type="textarea"
            :autosize="{ minRows: 1, maxRows: 6}"
            v-model="menuParams.roles"
            placeholder="请输入"
            clearable
            style="width: 100%"
          />
        </el-form-item>
        <el-form-item label="图标：" prop="icon">
          <el-select v-model="menuParams.icon" placeholder="请选择" clearable filterable style="width: 100%">
            <el-option
              v-for="(item, key) in svgIcons"
              :key="key"
              :label="item"
              :value="item">
              <svg-icon :icon-class="item" class-name="disabled"/>
              <span>{{ item }}</span>
            </el-option>
          </el-select>
        </el-form-item>
      </el-form>
      <div slot="footer">
        <el-button type="primary" @click="save"> {{menuParams.id > 0 ? '保存' : '新增'}}</el-button>
        <el-button type="info" @click="closeDialog">取消</el-button>
      </div>
    </el-dialog>

  </div>
</template>

<script>
  import {
    getList,
    save,
    remove,
  } from '@/api/system/menu';
  import svgIcons from '@/utils/svg-icons'
  import {
    BTN_MAP,
    BTN_MAP_MENU,
    BTN_MENU_ADD,
    BTN_MENU_EDIT,
    BTN_MENU_DEL,
  } from '@/api/btn'
  import {deepClone} from "../../../../utils";

  export default {
    name: "MenuManage",

    props: {
      systemId: {
        type: String,
        default: {}
      },
      system: {
        type: String,
        default: {}
      },
      menuTypeOption: {
        type: Object,
        default: {}
      },
    },

    data() {
      return {
        BTN_MAP,
        BTN_MAP_MENU,
        BTN_MENU_ADD,
        BTN_MENU_EDIT,
        BTN_MENU_DEL,
        svgIcons,

        loading: false,
        isExpansion: true,

        filterText: '',
        showDialog: false,
        dialogTitle: '添加菜单',
        menuParams: {
          id: 0,
          menuName: '',
          title: '',
          type: "2",
          parentId: '',
          icon: '',
          roles: '',
        },
        menuRules: {
          menuName: [{required: true, message: '请输入Name', trigger: 'change'}],
          type: [{required: true, message: '请选择菜单类型', trigger: 'change'}],
          title: [{required: true, message: '请输入菜单名称', trigger: 'change'}],
          roles: [{required: true, message: '请选择权限标识', trigger: 'change'}],
        },

        defaultProps: {
          expandTrigger: 'hover',
          label: 'title',
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

        const selectedId = this.menuParams?.id || 0;
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
        const ret = await getList({systemId: this.systemId});
        this.list = ret?.data || [];
        this.loading = false;
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
              match |= item[pro].toLowerCase().includes(searchValue.toLowerCase());
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
        this.$confirm('此操作将永久删除该菜单, 是否继续?', '提示', {
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
        this.$refs.menuParams.validate(valid => {
          if (valid) {
            this.loading = true;
            this.menuParams.systemId = this.systemId;
            save(this.menuParams).then((res) => {
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
        this.menuParams = {
          id: row.id,
          menuName: row.menuName,
          title: row.title,
          type: row.type.toString(),
          parentId: row.parentId,
          icon: row.icon,
          roles: row.roles,
        };
        this.dialogTitle = '编辑';
        this.showDialog = true;
      },

      closeDialog() {
        this.showDialog = false;
        this.menuParams = {
          id: 0,
          type: "2",
        };
        location.reload();
      },

      menuRowClassName({row, rowIndex}) {
        if (this.filterText) {
          if (row.title.toLowerCase().indexOf(this.filterText.toLowerCase()) !== -1
          || row.menuName.toLowerCase().indexOf(this.filterText.toLowerCase()) !== -1
          || row.roles.toLowerCase().indexOf(this.filterText.toLowerCase()) !== -1) {
            return 'search-row';
          }
        }
        return '';
      },
      // 切换数据表格树形展开
      toggleRowExpansion() {
        this.isExpansion = !this.isExpansion;
        this.toggleRowExpansionAll(this.list, this.isExpansion);
      },
      toggleRowExpansionAll(data, isExpansion) {
        data.forEach((item) => {
          this.$refs.menuTree.toggleRowExpansion(item, isExpansion);
          if (item.children !== undefined && item.children !== null) {
            this.toggleRowExpansionAll(item.children, isExpansion);
          }
        });
      },
    }
  }
</script>

<style>
  .el-table .search-row {
    color: #F56C6C !important;
  }

</style>
<style scoped>

</style>

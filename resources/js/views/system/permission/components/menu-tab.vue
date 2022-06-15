<template>
  <div>
    <!--表单-->
    <el-card>
      <el-input
        v-model="filterText"
        clearable
        placeholder="输入菜单|Name|权限标识进行搜索"
        style="width: 100%;"
      >
      </el-input>
      <el-table
        ref="menuTree"
        :data="treeData"
        :max-height="tableHeight"
        :row-class-name="menuRowClassName"
        :tree-props="{children: 'children', hasChildren: 'hasChildren'}"
        default-expand-all
        row-key="id"
      >
        <el-table-column align="center" fixed label="菜单ID" prop="id" type="" width="100"/>
        <el-table-column align="left" fixed label="菜单" prop="title" width="200">
          <template slot="header" slot-scope="scope">
            <span>菜单 </span>
            <el-button :icon="isExpansion ? 'el-icon-folder-remove' : 'el-icon-folder-add'" style="margin-left: 20px;"
                       type="text"
                       @click="toggleRowExpansion">
              全部{{ isExpansion ? "收缩" : "展开" }}
            </el-button>
          </template>
        </el-table-column>
        <el-table-column fixed label="Name" prop="menuName" width="300"/>
        <el-table-column label="Trans" prop="localeName" width="300">
          <template slot="header" slot-scope="scope">
            <el-select v-model="locale" size="mini" style="width: 200px">
              <el-option v-for="(v, k) in languages" :key="k" :value="v" :label="v"/>
            </el-select>
          </template>
          <template slot-scope="scope">
            <div style="width: 200px; float: left">{{  scope.row.trans[locale] || scope.row.title}}</div>
            <el-button v-permission="[BTN_MENU_TRANS]" type="text" @click="showTrans(scope.row, scope.$index)">
              {{ BTN_MAP_MENU[BTN_MENU_TRANS] }}
            </el-button>
          </template>
        </el-table-column>
        <el-table-column align="center" label="type" prop="类型" width="150">
          <template slot-scope="scope">
            <el-tag :type="`${(scope.row.type === 1 && 'info') ||
                  (scope.row.type === 2 && 'success') ||
                  (scope.row.type === 3 && 'warning')}`">
              {{ menuTypeOption[scope.row.type] }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column align="center" label="上级ID" prop="parentId" width="150"/>
        <el-table-column align="center" label="图标" prop="icon" width="150">
          <template slot-scope="scope">
            <svg-icon :icon-class="scope.row.icon"/>
          </template>
        </el-table-column>
        <el-table-column align="left" label="权限标识" prop="roles" width="300"/>
        <el-table-column align="center" label="创建时间" prop="createdAt" width="160"/>
        <el-table-column align="center" label="更新时间" prop="updatedAt" width="160"/>
        <el-table-column align="center" fixed="right" label="操作" width="200">
          <template slot="header" slot-scope="scope">
            <span>操作 </span>
            <el-button
              v-permission="[BTN_MENU_ADD]"
              class="el-icon-plus"
              style="margin-left: 20px;"
              type="text"
              @click="showDialog=true"
            >
              {{ BTN_MAP_MENU[BTN_MENU_ADD] }}
            </el-button>
          </template>
          <template v-slot="scope">
            <el-button v-permission="[BTN_MENU_EDIT]" icon="el-icon-edit" type="text" @click="showEdit(scope.row)">
              {{ BTN_MAP_MENU[BTN_MENU_EDIT] }}
            </el-button>
            <el-button v-permission="[BTN_MENU_DEL]" :disabled="scope.row.children !== undefined" icon="el-icon-delete"
                       style="color:#F56C6C;" type="text" @click="remove(scope.row.id)">
              {{ BTN_MAP_MENU[BTN_MENU_DEL] }}
            </el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>

    <!--编辑弹窗-->
    <el-dialog
      :before-close="closeDialog"
      :close-on-click-modal="false"
      :destroy-on-close="true"
      :title="dialogTitle"
      :visible.sync="showDialog"
    >
      <el-form ref="menuParams" v-loading="loading" :model="menuParams" :rules="menuRules"
               label-position="right" label-width="120px">
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
              <i slot="reference" class="el-icon-question"></i>
            </el-popover>
            <span>：</span>
          </template>
          <el-input v-model="menuParams.menuName" clearable placeholder="Name 需与代码中保持一致！" style="width: 100%"/>
        </el-form-item>
        <el-form-item label="上级菜单：" prop="parentId">
          <template slot="label">
            <span>上级菜单</span>
            <el-popover trigger="hover">
              <div>
                此内容请跟开发人员确认，需要与代码中保持一致！
              </div>
              <i slot="reference" class="el-icon-question"></i>
            </el-popover>
            <span>：</span>
          </template>
          <el-cascader
            v-model="menuParams.parentId"
            :options="selectOptions"
            :props="defaultProps"
            clearable
            filterable
            placeholder="上级菜单 需与代码中保持一致！"
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
          <el-input v-model="menuParams.title" clearable placeholder="请输入" style="width: 100%"/>
        </el-form-item>
        <el-form-item label="权限标识：" prop="roles">
          <el-select v-if="menuParams.type === '3'" v-model="menuParams.roles" filterable placeholder="请选择"
                     style="width: 100%">
            <el-option
              v-for="(item, key) in BTN_MAP"
              :key="key"
              :label="key"
              :value="key">
            </el-option>
          </el-select>
          <el-input
            v-else
            v-model="menuParams.roles"
            :autosize="{ minRows: 1, maxRows: 6}"
            clearable
            placeholder="请输入"
            style="width: 100%"
            type="textarea"
          />
        </el-form-item>
        <el-form-item label="图标：" prop="icon">
          <el-select v-model="menuParams.icon" clearable filterable placeholder="请选择" style="width: 100%">
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
        <el-button type="primary" @click="save"> {{ menuParams.id > 0 ? '保存' : '新增' }}</el-button>
        <el-button type="info" @click="closeDialog">取消</el-button>
      </div>
    </el-dialog>

    <el-dialog
      v-if="transDialog"
      :close-on-click-modal="false"
      :destroy-on-close="true"
      :visible.sync="transDialog"
      title=""
    >
      <menu-trans
        :trans-row="transRow"
        :locale="locale"
        @closeTrans="closeTrans"
      />
    </el-dialog>
  </div>
</template>

<script>
  import {
    getList,
    add,
    edit,
    remove,
  } from '@/api/system/menu';
  import svgIcons from '@/utils/svg-icons'
  import {
    BTN_MAP,
    BTN_MAP_MENU,
    BTN_MENU_ADD,
    BTN_MENU_EDIT,
    BTN_MENU_DEL,
    BTN_MENU_TRANS,
  } from '@/api/btn'
  import {deepClone} from "@/utils";
  import MenuTrans from "./menu-trans"

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
    components: {
      MenuTrans
    },
    mounted() {
      this.$nextTick(() => {
        this.tableHeight = window.innerHeight - 250;
      })
    },
    data() {
      return {
        BTN_MAP,
        BTN_MAP_MENU,
        BTN_MENU_ADD,
        BTN_MENU_EDIT,
        BTN_MENU_DEL,
        BTN_MENU_TRANS,
        svgIcons,

        loading: false,
        tableHeight: 0,
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
        transRow: {},
        transIndex: 0,
        transDialog: false,
        locale: 'zh',
        languages: [
          'zh',
          'en',
          'zh_TW'
        ]
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
      this.locale = this.$store.state.settings.locale;
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
            const func = this.menuParams.id > 0 ? edit : add;
            func(this.menuParams).then((res) => {
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
      showTrans(row, index) {
        this.transRow = row;
        this.transIndex = index;
        this.transDialog = true;
      },
      closeTrans() {
        this.transDialog = false;
      }
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

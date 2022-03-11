<template>
  <div class="app-container">
    <el-card>
      <el-form
        :inline="true"
        label-position="right"
        label-width="100px"
      >
        <el-row>
          <el-form-item label="数据源:">
            <el-input v-model="queryParams.resou" @keyup.enter.native="queryList" class="form-item-width"
                      placeholder="支持模糊搜索"/>
          </el-form-item>
          <el-form-item label="手机号:">
            <el-input v-model="queryParams.mobile" @keyup.enter.native="queryList" class="form-item-width"
                      placeholder="支持模糊搜索"/>
          </el-form-item>
          <el-form-item label=" ">
            <el-button type="primary" @click="queryList">查询</el-button>
            <el-button type="primary" icon="el-icon-plus" @click="showDialog=true">新增</el-button>
          </el-form-item>
        </el-row>
      </el-form>
      <el-table
        :data="result.list"
        border
        height="600px"
      >
        <el-table-column fixed label="权限ID" prop="id" width="80px" align="center"/>
        <el-table-column fixed label="菜单" prop="menuName" width="200px" align="left"/>
        <el-table-column fixed label="数据源" prop="resource" width="120px" align="left"/>
        <el-table-column fixed label="数据权限名称" prop="dataName" width="200px" align="left"/>
        <el-table-column label="数据权限标识" prop="dataMark" width="200px" align="left"/>
        <el-table-column label="条件" prop="conditions" width="400px" align="left">
          <template slot-scope="scope">
            <b-ace-editor
              :value="handleJsonFormat(scope.row.conditions)"
              :wrap="true"
              :readonly="true"
              :options="editorOptions"
              lang="json"
              width="100%"
            ></b-ace-editor>
          </template>
        </el-table-column>
        <el-table-column label="字段" prop="fields" width="200px" align="left">
          <template slot-scope="scope">
            <b-ace-editor
              v-model="scope.row.fields"
              :wrap="true"
              :readonly="true"
              :options="editorOptions"
              lang="json"
              width="100%"
            />
          </template>
        </el-table-column>
        <el-table-column label="创建时间" prop="createdAt" width="150px" align="center"/>
        <el-table-column label="修改时间" prop="updatedAt" width="150px" align="center"/>
        <el-table-column fixed="right" label="操作" align="center" width="200px">
          <template slot-scope="scope">
            <div>
              <el-button type="text" @click="showEdit(scope.row)">编辑</el-button>
            </div>
          </template>
        </el-table-column>
      </el-table>
    </el-card>
    <el-card>
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
    </el-card>

    <el-drawer
      :title="dialogTitle"
      direction="rtl"
      size="50%"
      :visible.sync="showDialog"
      :destroy-on-close="true"
      :wrapper-closable="false"
      :before-close="closeDialog"
      custom-class="overflow-auto"
    >
      <div class="app-container">
        <el-form
          ref="permissionParams"
          v-loading="loading"
          :rules="permissionRules"
          :model="permissionParams"
          label-width="120px"
          label-position="top"
        >
          <el-form-item label="System：" prop="systemId">
            <el-input v-model="system" placeholder="请输入" readonly style="width: 100%"/>
          </el-form-item>
          <el-form-item label="菜单：" prop="menuId">
            <el-cascader
              v-model="permissionParams.menuId"
              :options="selectOptions"
              :props="defaultProps"
              placeholder="请选择"
              filterable
              clearable
              style="width: 100%"
            />
          </el-form-item>
          <el-form-item label="数据源：" prop="resource">
            <el-input v-model="permissionParams.resource" placeholder="请输入" clearable style="width: 100%"/>
          </el-form-item>
          <el-form-item label="数据权限名称：" prop="dataName">
            <el-input v-model="permissionParams.dataName" placeholder="请输入" clearable style="width: 100%"/>
          </el-form-item>
          <el-form-item label="数据权限标识：" prop="dataMark">
            <el-input v-model="permissionParams.dataMark" placeholder="请输入" clearable style="width: 100%"/>
          </el-form-item>
          <el-form-item label="数据权限条件：" prop="conditions">
            <b-ace-editor v-model="permissionParams.conditions" lang="json" width="100%"/>
          </el-form-item>
          <el-form-item label="数据权限字段：" prop="fields">
            <b-ace-editor v-model="permissionParams.fields" lang="json" width="100%"/>
          </el-form-item>
          <el-form-item label=" " align="right">
            <el-button type="primary" @click="save"> {{permissionParams.id > 0 ? '保存' : '新增'}}</el-button>
            <el-button type="info" @click="closeDialog">取消</el-button>
          </el-form-item>
        </el-form>
      </div>
    </el-drawer>
  </div>
</template>

<script>
  import {
    getList,
    save,
    remove,
  } from '@/api/system/data-permission';
  import {
    getList as getMenuList,
  } from '@/api/system/menu';
  import {
    handleJsonFormat
  } from '@/utils/index';


  export default {
    name: "DataPermissionManage",

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

    components: {},

    data() {
      const checkObj = (rule, value, callback) => {
        try {
          if (JSON.parse(value.trim())) {
            callback()
          }
        } catch (e) {
          callback('不是标准json')
        }
      };

      return {
        handleJsonFormat,
        loading: false,
        menuList: [],

        editorOptions: {
          printMarginColumn: 0,
          minLines: 10,
          maxLines: 15,
        },

        queryParams: {
          menuId: '',
          resource: '',
          dataMark: '',
          dataName: '',
          page: 1,
          pageSize: 20,
        },
        pageSizes: [10, 20, 50, 100, 200],
        result: {
          list: [],
          cnt: 0
        },

        showDialog: false,
        dialogTitle: '添加权限',
        permissionParams: {
          id: 0,
          menuId: '',
          resource: '',
          dataMark: "",
          dataName: '',
          conditions: '[]',
          fields: '[]',
        },
        permissionRules: {
          menuId: [{required: true, message: '请选择菜单', trigger: 'change'}],
          resource: [{required: true, message: '请输入数据源', trigger: 'change'}],
          dataMark: [{required: true, message: '请输入数据权限标识', trigger: 'change'}],
          dataName: [{required: true, message: '请输入数据权限名称', trigger: 'change'}],
          conditions: [{validator: checkObj, trigger: 'blur'}],
          fields: [{validator: checkObj, trigger: 'blur'}],
        },

        defaultProps: {
          expandTrigger: 'hover',
          label: 'title',
          value: 'id',
          emitPath: false,
          checkStrictly: true
        },
      }
    },

    computed: {
      selectOptions() {
        function dealDisabled(data) {
          const disabled = data?.type !== 2;
          if (typeof data === 'object') {
            data.disabled = disabled;
            const children = data?.children || [];
            if (children) {
              children.forEach(item => {
                dealDisabled(item)
              })
            }
          }
        }

        dealDisabled(this.menuList[0]);
        return this.menuList;
      }
    },

    async created() {
      const menuRet = await getMenuList({systemId: this.systemId});
      this.menuList = menuRet?.data || [];
      await this.queryList();
    },

    methods: {
      async queryList() {
        this.queryParams.page = 1;
        this.getList();
      },

      getList() {
        this.loading = true;
        this.queryParams.systemId = this.systemId;
        getList(this.queryParams).then((res) => {
          if (res.code > 0) {
            this.$message.error(res.msg)
          } else {
            this.queryParams.page = parseInt(res.data.page);
            this.queryParams.pageSize = parseInt(res.data.pageSize);
            this.result.cnt = parseInt(res.data.cnt);
            this.result.list = res.data.list;
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
        this.permissionParams = {
          id: row.id,
          menuId: row.menuId,
          resource: row.resource,
          dataMark: row.dataMark,
          dataName: row.dataName,
          conditions: handleJsonFormat(row.conditions),
          fields: handleJsonFormat(row.fields),
        };
        this.dialogTitle = '修改权限';
        this.showDialog = true;
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
        this.$refs.permissionParams.validate(valid => {
          if (valid) {
            this.loading = true;
            this.permissionParams.systemId = this.systemId;
            save(this.permissionParams).then((res) => {
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

      closeDialog() {
        this.showDialog = false;
        this.permissionParams = {
          id: 0,
          menuId: '',
          resource: '',
          dataMark: "",
          dataName: '',
          conditions: `[]`,
          fields: `[]`,
        };
      },
    }
  }
</script>


<template>
  <div class="app-container">
    <el-card>
      <div slot="header" class="clearfix">
        <span>部门管理</span>
        <el-button
          type="primary"
          class="el-icon-plus"
          style="float: right; margin-right: 20px;"
          @click="showDialog=true"
        >
          新增
        </el-button>
      </div>
      <div>
        <el-table
          :data="list"
          row-key="id"
          :tree-props="{children: 'children', hasChildren: 'hasChildren'}"
        >
          <el-table-column prop="name" label="部门">
            <template slot="header" slot-scope="scope">
              <span>部门</span>
            </template>
          </el-table-column>
          <el-table-column prop="id" label="部门ID"/>
          <el-table-column prop="parentId" label="PID"/>
          <el-table-column label="操作" align="center">
            <template slot-scope="scope">
              <el-button type="primary" @click="showEdit(scope.row)">编辑</el-button>
              <el-button type="danger" @click="remove(scope.row.id)">删除</el-button>
            </template>
          </el-table-column>
        </el-table>
      </div>
    </el-card>

    <el-dialog
      :title="dialogTitle"
      :visible.sync="showDialog"
      :destroy-on-close="true"
      :close-on-click-modal="false"
      :before-close="closeDialog"
    >
      <el-form ref="departmentParams" v-loading="loading" :rules="departmentRules" :model="departmentParams"
               label-width="120px" label-position="right">
        <el-form-item label="上级部门：" prop="parentId">
          <el-cascader
            v-model="departmentParams.parentId"
            :options="selectOptions"
            :props="defaultProps"
            filterable
            clearable
            style="width: 100%"
          />
        </el-form-item>
        <el-form-item label="部门：" prop="name">
          <el-input v-model="departmentParams.name" placeholder="请输入" clearable style="width: 100%"/>
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
  import {getList, save, remove} from '@/api/system/department';

  export default {
    name: "DepartmentManage",

    data() {
      return {
        loading: false,

        showDialog: false,
        dialogTitle: '添加部门',
        departmentParams: {
          id: 0,
          name: '',
          parentId: ''
        },
        editDialog: true,
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
          console.log(id, data, disabled);
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
      }
    },

    async created() {
      await this.queryList();
      if (this.list.length > 0) {
        this.$nextTick(() => {
          document.getElementsByClassName('el-table__expand-icon')[0].click();
        });
      }
    },

    methods: {
      async queryList() {
        this.loading = true;
        const ret = await getList();
        this.list = ret?.data || [];
        this.loading = false;
      },
      async remove(id) {
        this.$confirm('此操作将永久删除该部门, 是否继续?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          this.loading = true;
          remove({id}).then((res) => {
            if (res.data.code > 0) {
              this.$message.error(res.data.msg)
            } else {
              this.$message.success('操作成功');
              this.queryList();
            }
            this.loading = false
          }).catch((e) => {
            this.$message.error('操作失败：' + e);
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
            save(this.departmentParams).then((res) => {
              if (res.data.code > 0) {
                this.$message.error(res.data.msg)
              } else {
                this.$message.success('操作成功');
                this.closeDialog();
                this.queryList();
              }
              this.loading = false
            }).catch((e) => {
              this.$message.error('新增出错：' + e);
              this.loading = false
            })
          } else {
            console.log('error submit!!');
            return false
          }
        });
      },

      showEdit(row) {
        this.departmentParams = row;
        this.dialogTitle = '编辑';
        this.showDialog = true;
      },

      closeDialog() {
        this.showDialog = false;
        this.departmentParams = {};
      }
    }
  }
</script>

<style scoped>

</style>

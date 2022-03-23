<template>
  <div class="app-container">
    <div>
      <el-form ref="roleParams" v-loading="loading" :rules="roleRules" :model="roleParams"
               label-width="120px" label-position="right">

        <el-form-item label="上级角色：" prop="parentId">
          <el-cascader
            v-model="roleParams.parentId"
            :options="selectOptions"
            :props="defaultProps"
            placeholder="请选择！"
            filterable
            clearable
            style="width: 100%"
          />
        </el-form-item>
        <el-form-item label="角色：" prop="roleName">
          <el-input v-model="roleParams.roleName" placeholder="请输入角色名！" clearable style="width: 100%"/>
        </el-form-item>
        <el-form-item label="描述：" prop="desc">
          <el-input v-model="roleParams.desc" type="textarea" autosize placeholder="请输入！" clearable
                    style="width: 100%"/>
        </el-form-item>
        <el-form-item label=" "  align="right">
          <el-button type="primary" @click="save"> {{roleParams.id > 0 ? '保存' : '新增'}}</el-button>
          <el-button type="info" @click="closeDialog">取消</el-button>
        </el-form-item>
      </el-form>
    </div>
  </div>
</template>

<script>
  import {save} from '@/api/system/role';

  export default {
    name: "RoleForm",

    props: {
      closeDialog: {
        type: Function,
        default: null
      },
      roleParams: {
        type: Object,
        default: {
          id: 0,
          parentId: '',
          roleName: '',
          desc: '',
        }
      },
      selectOptions: {
        type: Array,
        default: [],
      },
      defaultProps: {
        type: Object,
        default: {}
      },
    },

    computed: {
    },

    data() {
      return {
        loading: false,

        roleRules: {
          roleName: [{required: true, message: '请输入角色名', trigger: 'change'}],
        },
      }
    },

    methods: {
      async save() {
        this.$refs.roleParams.validate(valid => {
          if (valid) {
            this.loading = true;
            save(this.roleParams).then((res) => {
              if (res.code > 0) {
                this.$message.error(res.msg)
              } else {
                this.$message.success('操作成功');
                this.closeDialog();
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
    },
  }
</script>

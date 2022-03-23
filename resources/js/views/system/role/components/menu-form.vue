<template>
  <div class="app-container">
    <div>
      <el-form ref="roleParams" v-loading="loading" :model="roleHasMenuParams"
               label-width="120px" label-position="right">
        <el-form-item label=" "  align="right">
          <el-button type="primary" @click="save"> 保存</el-button>
          <el-button type="info" @click="closeDialog">取消</el-button>
        </el-form-item>
      </el-form>
    </div>
  </div>
</template>

<script>
  import {saveRoleHasMenu} from '@/api/system/role';

  export default {
    name: "RoleHasMenuForm",

    props: {
      closeDialog: {
        type: Function,
        default: null
      },
      roleHasMenuParams: {
        type: Object,
        default: {
          roleId: 0,
          menuIds: []
        }
      },
    },

    computed: {
    },

    data() {
      return {
        loading: false,
      }
    },

    methods: {
      async save() {
        this.$refs.roleParams.validate(valid => {
          if (valid) {
            this.loading = true;
            saveRoleHasMenu(this.roleHasMenuParams).then((res) => {
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

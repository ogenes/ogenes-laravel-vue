<template>
  <div class="app-container">
    <el-form
      ref="userParams"
      :model="userParams"
      :rules="userRules"
      label-position="top"
    >
      <el-form-item v-if="userParams.id > 0" label="用户ID：" prop="id">
        <el-input v-model="userParams.id" type="text" :disabled="true" style="width: 100%" />
      </el-form-item>
      <el-form-item label="头像：" prop="avatar">
        <el-input v-model="userParams.avatar" type="text" style="width: 100%" />
      </el-form-item>
      <el-form-item label="用户名：" prop="username">
        <el-input v-model="userParams.username" type="text" style="width: 100%" />
      </el-form-item>
      <el-form-item label="手机号：" prop="mobile">
        <el-input v-model="userParams.mobile" type="text" style="width: 100%" />
      </el-form-item>
      <el-form-item label="邮箱：" prop="email">
        <el-input v-model="userParams.email" type="text" style="width: 100%" />
      </el-form-item>
      <el-form-item label="部门：" prop="deptIds">
        <el-cascader
          v-model="userParams.deptIds"
          :options="departments"
          :props="defaultProps"
          :show-all-levels="false"
          :collapse-tags="true"
          placeholder="请选择"
          filterable
          clearable
          style="width: 100%"
        />
      </el-form-item>
      <el-form-item label="" align="right">
        <el-button type="primary" :loading="loading" @click="save">提交</el-button>
        <el-button type="info" @click="closeDialog">取消</el-button>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
  import { save } from '@/api/user';
  export default {
    name: "",

    props: {
      closeDialog: {
        type: Function,
        default: null
      },
      userParams: {
        type: Object,
        default: {
          id: 0,
          username: '',
          mobile: '',
          email: '',
          avatar: '',
          deptIds: [],
        }
      },
      departments: {
        type: Array,
        default: [],
      },
      defaultProps: {
        type: Object,
        default: {}
      },
    },

    data() {
      return {
        loading: false,

        userRules: {
          username: [{required: true, message: '请输入用户名', trigger: 'change'}],
          mobile: [{required: true, message: '请输入手机号', trigger: 'change'}],
          deptIds: [{required: true, message: '请选择部门', trigger: 'change'}]
        },
      }
    },

    methods: {
      async save() {
        this.$refs.userParams.validate(valid => {
          if (valid) {
            this.loading = true;
            save(this.userParams).then((res) => {
              if (res.data.code > 0) {
                this.$message.error(res.data.msg)
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

<style scoped>

</style>

<template>
  <div class="app-container">
    <el-form
      ref="userParams"
      :model="userParams"
      :rules="userRules"
      label-position="top"
    >
      <el-form-item v-if="userParams.id > 0" label="用户ID：" prop="id">
        <el-input v-model="userParams.id" type="text" :disabled="true" style="width: 100%"/>
      </el-form-item>
      <el-form-item label="头像：" prop="avatar">
        <el-upload
          class="avatar-uploader"
          action="eric"
          :http-request="fileUpload"
          :auto-upload="true"
          :show-file-list="false"
          :before-upload="beforeAvatarUpload"
        >
          <el-avatar
            v-if="userParams.avatar"
            :size="200"
            style="font-size: 100px;"
            :src="userParams.avatar"
          />
          <el-avatar v-else :size="200"> 点击上传</el-avatar>
        </el-upload>
      </el-form-item>
      <el-form-item label="用户名：" prop="username">
        <el-input v-model="userParams.username" type="text" style="width: 100%"/>
      </el-form-item>
      <el-form-item label="手机号：" prop="mobile">
        <el-input v-model="userParams.mobile" type="text" style="width: 100%"/>
      </el-form-item>
      <el-form-item label="邮箱：" prop="email">
        <el-input v-model="userParams.email" type="text" style="width: 100%"/>
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
  import {save} from '@/api/user';
  import {compressAccurately} from 'image-conversion';
  import {fileUpload} from '@/api/common'

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
      beforeAvatarUpload(file) {
        return new Promise((resolve, reject) => {
          const isType = file.type === 'image/jpeg' || file.type === 'image/png';
          if (isType) {
            const _URL = window.URL || window.webkitURL;
            const img = new Image();
            img.onload = function () {
              file.width = img.width;
              file.height = img.height;
              if (img.width > 2000 || file.size / 1024 > 1000) {
                compressAccurately(file, {
                  size: 1000,
                  width: 2000
                }).then(res => {
                  resolve(res);
                }).catch(() => {
                  this.$message.error('图片过大，自动压缩失败！');
                  reject();
                })
              } else {
                resolve(file);
              }
            };
            img.src = _URL.createObjectURL(file);
          } else {
            this.$message.error('上传图片只能是 JPG|PNG 格式!');
            reject();
          }
        })
      },

      async fileUpload(file) {
        const ret = await fileUpload(file, {'source': 'user-avatar'});
        if (ret.code > 0) {
          this.$message.error(ret.msg);
        } else {
          this.$message.success('上传成功！');
          console.log('ret.data.path', ret.data.path);
          this.userParams.avatar = ret.data.path;
        }
      },
    },
  }
</script>

<style>
  .avatar-uploader {
    text-align: center;
  }
  .avatar-uploader .el-upload {
    border: 1px solid #d9d9d9;
    border-radius: 50%;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    width: 200px;
    height: 200px;
  }

  .avatar-uploader .el-upload:hover {
    border-color: #6E7277;
  }
</style>

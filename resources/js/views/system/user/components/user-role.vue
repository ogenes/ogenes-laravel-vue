<template>
  <div class="app-container">
    <div>
      <el-form ref="userHasRoleParams" v-loading="loading" :model="userHasRoleParams" label-position="top">
        <el-input
          placeholder="输入关键字进行过滤"
          v-model="filterText"
          clearable
          style="width: 800px;"
        >
        </el-input>
        <el-tree
          ref="roleTree"
          style="border: 1px solid #DCDFE6; width: 800px; border-radius: 5px;padding-top: 10px;"
          :data="roleTree"
          default-expand-all
          show-checkbox
          :check-strictly="true"
          node-key="id"
          :default-checked-keys="userHasRoleParams.roleIds"
          :props="{ expandTrigger: 'hover', label: 'roleName', value: 'id' }"
          @check-change="handleCheckChange"
          :filter-node-method="filterNode"
        >
        </el-tree>
        <el-form-item label=" " align="right">
          <el-button type="primary" @click="save">保存</el-button>
          <el-button type="info" @click="closeDialog">取消</el-button>
        </el-form-item>
      </el-form>
    </div>
  </div>
</template>

<script>
  import {
    saveUserHasRole
  } from '@/api/user';

  export default {
    name: "UserHasRoleForm",

    props: {
      closeDialog: {
        type: Function,
        default: null
      },
      userHasRoleParams: {
        type: Object,
        default: {
          uid: 0,
          roleIds: [],
        }
      },
      roleTree: {
        type: Array,
        default: []
      },
    },

    watch: {
      filterText(val) {
        this.$refs.roleTree.filter(val);
      }
    },

    computed: {},

    data() {
      return {
        loading: false,

        filterText: '',
      }
    },

    async created() {},

    methods: {
      filterNode(value, data) {
        if (!value) return true;
        return data.roleName.indexOf(value) !== -1;
      },

      handleCheckChange() {
        const res = this.$refs.roleTree.getCheckedNodes();
        const arr = [];
        res.forEach((item) => {
          arr.push(item.id)
        });
        const newArr = [];
        const len = arr.length;
        for (let i = 0; i < len; i++) {
          if (newArr.indexOf(arr[i]) === -1) {
            newArr.push(arr[i]);
          }
        }
        this.userHasRoleParams.roleIds = newArr;
      },

      async save() {
        this.$refs.userHasRoleParams.validate(valid => {
          if (valid) {
            this.loading = true;
            saveUserHasRole(this.userHasRoleParams).then((res) => {
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

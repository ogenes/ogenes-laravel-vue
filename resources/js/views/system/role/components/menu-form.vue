<template>
  <div class="app-container">
    <div>
      <el-form ref="roleParams" v-loading="loading" :model="roleHasMenuParams" label-position="top">
        <el-tabs tab-position="top">
          <el-tab-pane
            v-for="(item, key) in menuTree"
            :key="key"
            :label="item.systemName"
          >
            <el-input
              placeholder="输入关键字进行过滤"
              v-model="filterText"
              clearable
              style="width: 800px;"
            >
            </el-input>
            <el-tree
              :ref="`menuTree` + item.systemId"
              style="border: 1px solid #DCDFE6; width: 800px; border-radius: 5px;padding-top: 10px;"
              :data="item.menu"
              default-expand-all
              show-checkbox
              :check-strictly="true"
              node-key="id"
              :default-checked-keys="roleHasMenuParams.menuIds"
              :props="{ expandTrigger: 'hover', label: 'title', value: 'id' }"
              @check-change="handleCheckChange"
              :filter-node-method="filterNode"
            >
            <span slot-scope="{ node, data }" class="custom-tree-node">
              <span>{{ node.label }} <span style="font-size: 5px;color: gray;">({{ data.menuName }})</span></span>
            </span>
            </el-tree>
            <el-form-item label=" " align="right">
              <el-button type="primary" @click="save">保存</el-button>
              <el-button type="info" @click="closeDialog">取消</el-button>
            </el-form-item>

          </el-tab-pane>
        </el-tabs>
      </el-form>
    </div>
  </div>
</template>

<script>
  import {
    saveRoleHasMenu
  } from '@/api/system/role';

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
          systemId: 1,
          menuIds: []
        }
      },
      menuTree: {
        type: Object,
        default: {}
      },
    },

    watch: {
      filterText(val) {
        for(const i in this.menuTree) {
          const systemId = this.menuTree[i]?.systemId || 0;
          this.$refs[`menuTree` + systemId][0].filter(val);
        }
      }
    },

    computed: {},

    data() {
      return {
        loading: false,

        filterText: '',
      }
    },

    async created() {
      console.log(this.menuTree, 'this.menuTree');
    },

    methods: {
      filterNode(value, data) {
        if (!value) return true;
        return data.title.indexOf(value) !== -1;
      },

      handleCheckChange(node, checked) {
        const systemId = node.systemId;
        const res = this.$refs[`menuTree` + systemId][0].getCheckedNodes();
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
        if (checked && node.parentId > 0) {
          newArr.push(node.parentId)
        }
        this.roleHasMenuParams.menuIds = newArr;
        this.roleHasMenuParams.systemId = systemId;
      },

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

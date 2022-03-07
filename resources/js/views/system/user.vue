<template>
  <div class="app-container">
    <el-card>
      <div slot="header">
        <span>用户管理</span>
      </div>
      <el-form
        :inline="true"
        label-position="right"
        label-width="100px"
      >
        <el-row>
          <el-form-item label="用户名:">
            <el-input v-model="queryParams.username" @keyup.enter.native="queryList" class="form-item" placeholder="支持模糊搜索"/>
          </el-form-item>
          <el-form-item label="手机号:">
            <el-input v-model="queryParams.mobile" @keyup.enter.native="queryList" class="form-item" placeholder="支持模糊搜索"/>
          </el-form-item>
          <el-form-item label="状态:">
            <el-select v-model="queryParams.userStatus" clearable class="form-item">
              <el-option v-for="(v, k) in userStatusOption" :key="k" :value="v.value" :label="v.label" />
            </el-select>
          </el-form-item>

          <el-form-item label="部门：" prop="deptIds">
            <el-cascader
              v-model="queryParams.deptIds"
              :options="departments"
              :props="defaultProps"
              :show-all-levels="false"
              :collapse-tags="true"
              placeholder="请选择"
              filterable
              clearable
              class="form-item"
            />
          </el-form-item>
          <el-form-item label=" ">
            <el-button type="primary" @click="queryList">查询</el-button>
            <el-button type="primary" icon="el-icon-plus" @click="showDialog=true">新增</el-button>
          </el-form-item>
        </el-row>
      </el-form>
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
      <el-table
        :data="result.list"
        border
        height="600px"
      >
        <el-table-column fixed label="用户ID" prop="uid" width="80px" align="center"/>
        <el-table-column fixed label="用户名" prop="avatar" width="120px" align="center">
          <template slot-scope="scope">
            <el-popover
              v-if="scope.row.avatar"
              placement="right-start"
              trigger="hover">
              <el-avatar :size="200" :src="scope.row.avatar+'?imageView2/1/w/80/h/80'"/>
              <el-avatar slot="reference" :src="scope.row.avatar+'?imageView2/1/w/80/h/80'"/>
            </el-popover>
            <el-avatar v-else icon="el-icon-user-solid"/>
            <div>
              {{ scope.row.username }}
            </div>
          </template>
        </el-table-column>
        <el-table-column fixed label="用户账号" prop="account" width="120px" align="center"/>
        <el-table-column label="手机号" prop="mobile" width="120px" align="center"/>
        <el-table-column label="邮箱" prop="email" width="200px" align="left"/>
        <el-table-column label="状态" prop="userStatus" width="100px" align="center">
          <template slot-scope="scope">
            <el-tag v-if="scope.row.userStatus > 0" type="success">正常</el-tag>
            <el-tag v-else type="danger">禁用</el-tag>
          </template>
        </el-table-column>
        <el-table-column label="部门" prop="departments" width="500px" align="left">
          <template slot-scope="scope">
            <div v-for="(item, key) in scope.row.departments" :key="key" v-if="key < 3">
              <span class="span-color">{{ item }}</span>
            </div>
            <el-popover
              v-if="scope.row.departments.length > 3"
              placement="top-end"
              trigger="hover">
              <div v-for="(item, key) in scope.row.departments" :key="key">
                <span class="span-color">{{ item }}</span>
              </div>
              <div slot="reference">
                <span class="span-color">……</span>
              </div>
            </el-popover>
          </template>
        </el-table-column>
        <el-table-column label="最近一次登录时间" prop="lastLoginAt" width="160px" align="center"/>
        <el-table-column label="最近一次登录地" prop="lastLoginIp" width="150px" align="center"/>
        <el-table-column label="最近一次修改时间" prop="updatedAt" width="160px" align="center"/>
        <el-table-column fixed="right" label="操作" align="center" width="200px">
          <template slot-scope="scope">
            <div>
              <el-button type="primary">编辑</el-button>
              <el-button :disabled="scope.row.uid === 1" type="danger">禁用</el-button>
            </div>
          </template>
        </el-table-column>
      </el-table>
    </el-card>
  </div>
</template>

<script>
  import {getList as getDepartmentList} from '@/api/system/department';
  import {
    getList,
    USER_STATUS_OPTION
  } from '@/api/user';

  export default {
    name: "UserManage",

    data() {
      return {
        loading: false,

        departments: [],
        defaultProps: {
          expandTrigger: 'hover',
          label: 'name',
          value: 'id',
          emitPath: false,
          multiple: true,
          checkStrictly: true
        },

        queryParams: {
          page: 1,
          pageSize: 20,
          username: '',
          mobile: '',
          userStatus: '',
          deptIds: []
        },
        pageSizes: [20, 50, 100, 200],
        result: {
          list: [],
          cnt: 0
        },

        showDialog: false,
      }
    },

    computed: {
      userStatusOption() {
        return USER_STATUS_OPTION;
      }
    },

    async created() {
      const ret = await getDepartmentList();
      this.departments = ret?.data || [];
      await this.queryList();
    },

    methods: {
      async queryList() {
        this.queryParams.page = 1;
        this.getList();
      },

      getList() {
        this.loading = true;
        getList(this.queryParams).then((res) => {
          if (res.code > 0) {
            this.$message.error(res.data.msg)
          } else {
            this.queryParams.page = parseInt(res.data.page);
            this.queryParams.pageSize = parseInt(res.data.pageSize);
            this.result.cnt = parseInt(res.data.cnt);
            this.result.list = res.data.list;
            console.log(this.result);
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
        this.getList()
      },
    }
  }
</script>

<style scoped lang="scss">
.app-container {
  .form-item {
    width: 300px
  }

  .page-position {
    text-align: right;
    margin: 10px 0;
  }

  .span-color {
    color: #1482f0
  }
}
</style>

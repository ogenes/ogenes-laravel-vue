<template>
  <div class="app-container">
    <el-card>
      <div style="float: right">
        <el-popover trigger="click" placement="bottom-start">
          <el-form :inline="true" label-width="100px" label-position="right">
            <el-row>
              <el-form-item label="资源:">
                <el-select v-model="queryParams.resourceArr" placeholder="请选择！" clearable multiple
                           class="form-item-width">
                  <el-option v-for="(v, k) in options.resourceMap" :key="k" :value="k" :label="v"/>
                </el-select>
              </el-form-item>
              <el-form-item label="操作人:">
                <el-select v-model="queryParams.uidArr" placeholder="请选择！" clearable multiple class="form-item-width">
                  <el-option v-for="(v, k) in options.users" :key="k" :value="v.uid" :label="v.username"/>
                </el-select>
              </el-form-item>
              <el-form-item label="创建时间:" prop="createdAt">
                <el-date-picker
                  v-model="queryParams.createdAt"
                  type="daterange"
                  style="width: 300px;"
                  range-separator="至"
                  start-placeholder="开始日期"
                  end-placeholder="结束日期"
                  value-format="yyyy-MM-dd"
                >
                </el-date-picker>
              </el-form-item>
            </el-row>
            <el-row>
              <el-form-item label="类型:">
                <el-input v-model="queryParams.type" clearable @keyup.enter.native="queryList"
                          class="form-item-width"
                          placeholder="支持模糊搜索"/>
              </el-form-item>
              <el-form-item label="备注:">
                <el-input v-model="queryParams.remark" clearable @keyup.enter.native="queryList"
                          class="form-item-width"
                          placeholder="支持模糊搜索"/>
              </el-form-item>
              <el-form-item label=" ">
                <el-button type="primary" @click="queryList">查询</el-button>
                <el-button type="info" @click="clearFilter">清空</el-button>
              </el-form-item>
            </el-row>
          </el-form>
          <el-button slot="reference" type="text" :style="hasFilter ? 'color: #67C23A' : 'color: #909399'"
                     icon="el-icon-search">
            筛选
          </el-button>
        </el-popover>
      </div>
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
        v-loading.fullscreen.lock="loading"
        border
        :default-sort="queryParams.sort"
        @sort-change="sortChange"
        height="700px"
      >
        <el-table-column type="" prop="id" sortable="custom" width="100" align="center" label="ID"/>
        <el-table-column prop="resource" sortable="custom" width="200" align="center" label="资源">
          <template slot-scope="scope">
            <span>{{ options.resourceMap[scope.row.resource] }}({{ scope.row.resource }})</span>
          </template>
        </el-table-column>
        <el-table-column prop="resourceId" sortable="custom" width="150" align="center" label="资源ID"/>
        <el-table-column prop="resource" sortable="custom" width="200" align="center" label="操作人">
          <template slot-scope="scope">
            <span>{{ scope.row.username }}({{ scope.row.uid }})</span>
          </template>
        </el-table-column>
        <el-table-column prop="type" sortable="custom" width="200" align="center" label="操作类型"/>
        <el-table-column prop="remark" width="400" align="left" label="操作备注">
          <template slot-scope="scope">
            <el-popover trigger="hover">
              <pre>{{ handleJsonFormat(scope.row.remark) }}</pre>
              <div slot="reference">
                {{scope.row.remark}}
              </div>
            </el-popover>
          </template>
        </el-table-column>
        <el-table-column prop="createdAt" sortable="custom" width="200" align="center" label="创建时间"/>
      </el-table>
    </el-card>
  </div>
</template>

<script>
  import {getOptions, getList} from '@/api/system/action-log';
  import { handleJsonFormat } from '@/utils/index';
  export default {
    name: "ActionLogManage",

    components: {},

    data() {
      return {
        handleJsonFormat,

        loading: false,
        options: {
          users: [],
          resourceMap: [],
        },

        queryParams: {
          resourceArr: [],
          uidArr: [],
          type: '',
          remark: '',
          createdAt: [],
          sort: {
            prop: 'id',
            order: 'descending',
          },
          page: 1,
          pageSize: 20,
        },
        pageSizes: [10, 20, 50, 100, 200],
        result: {
          list: [],
          cnt: 0
        },
      }
    },

    computed: {
      hasFilter() {
        return !!(this.queryParams.type
          || this.queryParams.remark
          || this.queryParams.resourceArr.length > 0
          || this.queryParams.uidArr.length > 0
          || this.queryParams.createdAt.length > 0);

      },
    },

    async created() {
      await this.initOptions();
      await this.queryList();
    },

    methods: {
      async initOptions() {
        const ret = await getOptions();
        this.options.users = ret?.data?.users || [];
        this.options.resourceMap = ret?.data?.resourceMap || [];
      },
      async queryList() {
        this.queryParams.page = 1;
        this.getList();
      },

      getList() {
        this.loading = true;
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
      sortChange(col) {
        this.queryParams.sort = {
          prop: col.prop,
          order: col.order
        };
        this.queryList();
      },

      clearFilter() {
        this.queryParams = {
          resourceArr: [],
          uidArr: [],
          type: '',
          remark: '',
          createdAt: [],
          sort: {
            prop: 'id',
            order: 'descending',
          },
          page: 1,
          pageSize: 20,
        };
        this.queryList();
      },
    }
  }
</script>

<style scoped lang="scss">
  .app-container {
    .form-item-width {
      width: 300px
    }
  }
</style>

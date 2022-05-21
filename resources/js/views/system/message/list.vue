<template>
  <div>
    <el-card>
      <el-input
        placeholder="请输入关键字后按回车键搜索"
        v-model="queryParams.keyword"
        clearable
        style="width: 100%;"
        @keyup.enter.native="queryList"
      >
      </el-input>
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
        :max-height="tableHeight"
      >
        <el-table-column type="" prop="id" sortable="custom" width="100" align="center" label="ID"/>
        <el-table-column type="" prop="cat" sortable="custom" width="100" align="center" label="品类"/>
        <el-table-column prop="title" sortable="custom" width="200" align="left" label="标题">
          <template v-slot="scope">
            <span>{{ scope.row.title }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="top" sortable="custom" width="200" align="left" label="置顶">
          <template v-slot="scope">
            <span>{{ scope.row.top ? '是' : '否' }}</span>
          </template>
        </el-table-column>
        <el-table-column type="" prop="publisher" sortable="custom" width="100" align="center" label="公布人"/>
        <el-table-column type="" prop="publishTime" sortable="custom" width="100" align="center" label="公布时间"/>
        <el-table-column prop="desc" width="200" align="left" label="描述">
          <template v-slot="scope">
            <pre>{{ scope.row.desc }}</pre>
          </template>
        </el-table-column>
        <el-table-column prop="createdAt" sortable="custom" width="200" align="center" label="创建时间"/>
        <el-table-column prop="updatedAt" sortable="custom" width="200" align="center" label="更新时间"/>
        <el-table-column align="center" label="操作" width="120">
          <template v-slot="scope">
            <router-link :to="'/system/message/edit/'+scope.row.id">
              <el-button type="primary" size="small" icon="el-icon-edit">
                编辑
              </el-button>
            </router-link>
          </template>
        </el-table-column>
      </el-table>
    </el-card>
  </div>
</template>

<script>
  import {getList} from '@/api/system/message';

  export default {
    name: "MessageList",

    components: {},

    mounted() {
      this.$nextTick(() => {
        this.tableHeight = window.innerHeight - 165;
      })
    },

    data() {
      return {
        loading: false,
        tableHeight: 0,

        queryParams: {
          keyword: '',
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

    computed: {},

    async created() {
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

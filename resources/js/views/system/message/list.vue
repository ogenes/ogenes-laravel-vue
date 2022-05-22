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
        <el-table-column type="" prop="cat" width="100" align="center" label="品类"/>
        <el-table-column prop="title" sortable="custom" align="left" label="标题">
          <template v-slot="scope">
            <router-link :to="'/system/message/view/'+scope.row.id">
              <el-link>{{ scope.row.title }}</el-link>
            </router-link>
          </template>
        </el-table-column>
        <el-table-column prop="top" sortable="custom" width="120" align="center" label="置顶">
          <template v-slot="scope">
            <el-switch
              v-model="scope.row.top"
              active-text="是"
              inactive-text="否"
              active-color="#67C23A"
              inactive-color="#F56C6C"
              :disabled="!checkPermission([BTN_MSG_TOP])"
              @change="switchTop($event, scope.row)"
            >
            </el-switch>
          </template>
        </el-table-column>
        <el-table-column fixed="right" label="隐藏" prop="hidden" width="120px;" align="center">
          <template v-slot="scope">
            <el-switch
              v-model="scope.row.hidden"
              active-text="是"
              inactive-text="否"
              active-color="#67C23A"
              inactive-color="#F56C6C"
              :disabled="!checkPermission([BTN_MSG_HIDDEN])"
              @change="switchHidden($event, scope.row)"
            >
            </el-switch>
          </template>
        </el-table-column>
        <el-table-column type="" prop="publisher" sortable="custom" width="100" align="center" label="公布人"/>
        <el-table-column type="" prop="publishTime" sortable="custom" width="150" align="center" label="公布时间"/>
        <el-table-column prop="createdAt" sortable="custom" width="150" align="center" label="创建时间"/>
        <el-table-column prop="updatedAt" sortable="custom" width="150" align="center" label="更新时间"/>
        <el-table-column align="center" fixed="right" label="操作" width="120">
          <template v-slot="scope">
            <div v-permission="[BTN_MSG_EDIT]">
              <router-link :to="'/system/message/edit/'+scope.row.id">
                <el-button type="text" icon="el-icon-edit">
                  {{ BTN_MAP_MSG[BTN_MSG_EDIT] }}
                </el-button>
              </router-link>
            </div>
          </template>
        </el-table-column>
      </el-table>
    </el-card>
  </div>
</template>

<script>
  import {getList, switchHidden, switchTop} from '@/api/system/message';
  import checkPermission from "@/utils/permission";
  import {
    BTN_MAP_MSG,
    BTN_MSG_EDIT,
    BTN_MSG_TOP,
    BTN_MSG_HIDDEN,
  } from '@/api/btn'

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
        BTN_MAP_MSG,
        BTN_MSG_EDIT,
        BTN_MSG_TOP,
        BTN_MSG_HIDDEN,
        checkPermission,

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
            this.result.list.forEach(item => {
              item.top = item.top > 0;
              item.hidden = item.hidden > 0;
            })
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
      switchHidden($event, row) {
        const label = $event ? '隐藏' : '恢复';
        row.hidden = !row.hidden;
        this.$confirm('确认' + label + '消息?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          this.loading = true;
          switchHidden({id: row.id, hidden: $event}).then((res) => {
            if (res.code > 0) {
              this.$message.error(res.msg)
            } else {
              this.$message.success('操作成功');
              this.queryList();
            }
            this.loading = false
          }).catch((e) => {
            this.loading = false
          });
          this.loading = false;
        }).catch(() => {
          this.$message({
            type: 'info',
            message: '已取消'
          });
        });
      },
      switchTop($event, row) {
        const label = $event ? '置顶消息' : '取消置顶';
        row.top = !row.top;
        this.$confirm('确认' + label + '?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          this.loading = true;
          switchTop({id: row.id, top: $event}).then((res) => {
            if (res.code > 0) {
              this.$message.error(res.msg)
            } else {
              this.$message.success('操作成功');
              this.queryList();
            }
            this.loading = false
          }).catch((e) => {
            this.loading = false
          });
          this.loading = false;
        }).catch(() => {
          this.$message({
            type: 'info',
            message: '已取消'
          });
        });
      },
    }
  }
</script>

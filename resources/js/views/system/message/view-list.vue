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

      <div>
        <div v-for="(item, key) in result.list" :key="key" class="message-list">
          <div class="message-list-content">
            <div style="font-weight: bold; font-size: 16px; height: 34px; line-height: 34px;">
              <el-tag v-if="item.top" type="warning" style="margin-right: 5px;">置顶</el-tag>
              <el-tag v-if="item.hidden" type="danger" style="margin-right: 5px;">隐藏</el-tag>
              <router-link :to="'/system/message/view/'+item.id">
                {{ item.title }}
              </router-link>
            </div>
            <div style="font-size: 12px; height: 30px; line-height: 30px; width: 800px; overflow: hidden;">
              <router-link :to="'/system/message/view/'+item.id">
                {{ item.desc }}……
              </router-link>
            </div>
            <div style="height: 30px; line-height: 40px;font-size: 14px;">
              <div style="float: left; margin-right: 15px; color: #409EFF; width: 100px; border-bottom: 1px solid #DCDFE6;">
                <svg-icon icon-class="cat"/>
                <span>{{ item.cat }}</span>
              </div>
              <div style="float: left; margin-right: 15px; width: 150px; border-bottom: 1px solid #DCDFE6;">
                <i class="el-icon-user"/>
                <span> {{ item.publisher }}</span>
              </div>
              <div style="float: left;margin-right: 15px; width: 150px; border-bottom: 1px solid #DCDFE6;">
                <svg-icon icon-class="timer"/>
                {{ item.publishTime }}
              </div>
              <div v-permission="[BTN_MSG_EDIT]" style="float: left;margin-right: 15px; border-bottom: 1px solid #DCDFE6;">
                <router-link :to="'/system/message/edit/'+item.id">
                  <el-button type="text" icon="el-icon-edit">
                    {{ BTN_MAP_MSG[BTN_MSG_EDIT] }}
                  </el-button>
                </router-link>
              </div>
            </div>
          </div>
          <div class="message-list-image">
            <el-image
              ref="uploadImage"
              fit="fill"
              :src="item.banner"
              :preview-src-list="[item.banner]"
            />
          </div>

        </div>
      </div>
      <div class="page-position" style="float: right">
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
    </el-card>
  </div>
</template>

<script>
  import {getList} from '@/api/system/message';
  import {
    BTN_MAP_MSG,
    BTN_MSG_EDIT,
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

        loading: false,
        tableHeight: 0,
        selectedIds: [],
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
        console.log(this.selectedIds);
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
  .message-list {
    padding: 10px;
    margin-top: 20px;
    height: 129px;
    border: 1px solid #DCDFE6;
    border-radius: 5px;

    .message-list-content {
      float: left;
    }

    .message-list-image {
      float: right;
      width: 194px;
      height: 99px;
      border-radius: 5px;

      .el-image {
        border: 1px solid #DCDFE6;
        border-radius: 5px;
        height: 100%;
      }
    }
  }
</style>

<template>
  <div>
    <el-card>
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
      <div style="margin-top: 50px;">
        <div v-for="(item, key) in result.list" :key="key" class="message-list">
          <div class="message-list-content">
            <div style="font-weight: bold; font-size: 16px; height: 34px; line-height: 34px;">
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
              <div
                style="float: left; margin-right: 15px; color: #409EFF; width: 100px; border-bottom: 1px solid #DCDFE6;">
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
    </el-card>
  </div>
</template>

<script>
  import {getMessage} from '@/api/message';

  export default {
    name: "MessageList",

    props: {
      typeId: {
        type: Number
      },
      catId: {
        type: String
      }
    },
    components: {},

    data() {
      return {

        loading: false,
        selectedIds: [],
        queryParams: {
          type: 0,
          cat: 0,
          page: 1,
          pageSize: 5,
        },
        pageSizes: [5, 10, 20, 50],
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
        this.queryParams.type = this.typeId;
        this.queryParams.cat = this.catId;
        getMessage(this.queryParams).then((res) => {
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

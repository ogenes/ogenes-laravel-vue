<template>
  <div style="width: 80%">
    <el-table
      :data="result.list"
      v-loading="loading"
      v-el-table-infinite-scroll="nextPage"
      :height="tableHeight"
      highlight-current-row
    >
      <el-table-column type="" prop="id" label=" ">
        <template slot-scope="scope">
          <div>ID： {{ scope.row.id }}</div>
          <div>资源： {{ scope.row.resourceName }}</div>
          <div>操作类型： {{ scope.row.type }}</div>
          <div>操作时间： {{ scope.row.createdAt }}</div>
        </template>
      </el-table-column>
    </el-table>
  </div>
</template>

<script>
  import {getActionList} from "@/api/user";
  import elTableInfiniteScroll from 'el-table-infinite-scroll'

  export default {
    name: "ProfileLog",
    props: {
      tableHeight: {
        type: Number
      },
    },
    directives: {
      'el-table-infinite-scroll': elTableInfiniteScroll
    },
    data() {
      return {
        loading: false,
        result: {
          list: [],
          cnt: 0
        },
        page: 1,
        pageSize: 20
      }
    },
    created() {
      this.getList();
    },
    methods: {
      async getList() {
        this.loading = true;
        const res = await getActionList({page: this.page, pageSize: this.pageSize});
        this.result.cnt = parseInt(res?.data?.cnt || 0);
        this.result.list = this.result.list.concat(res?.data?.list || []);
        this.loading = false;
      },
      async nextPage() {
        if (this.pageSize * this.page >= this.result.cnt) {
          console.log('到底了')
          return
        }
        this.page++;
        this.getList();
      }
    }
  }
</script>

<style scoped>

</style>

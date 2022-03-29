<template>
  <div class="app-container">
    <el-card>
      <el-form :inline="true" label-width="80px" label-position="left">
        <el-row>
          <el-form-item label="字段名:">
            <el-input v-model="queryParams.dictName" clearable @keyup.enter.native="queryList" class="form-item-width"
                      placeholder="支持模糊搜索"/>
          </el-form-item>
          <el-form-item label="字段标识:">
            <el-input v-model="queryParams.symbol" clearable @keyup.enter.native="queryList" class="form-item-width"
                      placeholder="支持模糊搜索"/>
          </el-form-item>
          <el-form-item label="备注：" prop="remark">
            <el-input v-model="queryParams.remark" clearable @keyup.enter.native="queryList" class="form-item-width"
                      placeholder="支持模糊搜索"/>
          </el-form-item>
          <el-form-item label="创建时间:" prop="createdAt">
            <el-date-picker
              v-model="queryParams.createdAt"
              type="daterange"
              class="form-item-width"
              range-separator="至"
              start-placeholder="开始日期"
              end-placeholder="结束日期"
              value-format="yyyy-MM-dd"
            >
            </el-date-picker>
          </el-form-item>
          <el-form-item label=" ">
            <el-button type="primary" @click="queryList">查询</el-button>
            <el-button
              type="primary"
              class="el-icon-plus"
              style="float: right; margin-right: 20px;"
              @click="result.list.unshift(deepClone(defaultRow))"
            >
              新增
            </el-button>
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
        :default-sort = "{prop: 'id', order: 'descending'}"
        border
        height="600px"
      >
        <el-table-column type="" prop="id" sortable width="100" align="center" label="字典ID"/>
        <el-table-column prop="dictName" sortable width="150" align="center" label="字典名称">
          <template slot-scope="scope">
            <el-input v-if="scope.row.showEdit" v-model="scope.row.dictName"/>
            <span v-else> {{ scope.row.dictName }} </span>
          </template>
        </el-table-column>
        <el-table-column prop="symbol" sortable min-width="100" align="center" label="字典标识">
          <template slot-scope="scope">
            <el-input v-if="scope.row.showEdit" v-model="scope.row.symbol"/>
            <el-link v-else type="primary" @click="showData(scope.row)"> {{ scope.row.symbol }}</el-link>
          </template>
        </el-table-column>
        <el-table-column prop="remark" min-width="200" align="left" label="备注">
          <template slot-scope="scope">
            <el-input v-if="scope.row.showEdit" v-model="scope.row.remark" type="textarea" autosize/>
            <span v-else> {{ scope.row.remark }} </span>
          </template>
        </el-table-column>
        <el-table-column prop="createdAt" sortable min-width="160" align="center" label="创建时间"/>
        <el-table-column prop="updatedAt" sortable min-width="160" align="center" label="更新时间"/>
        <el-table-column fixed="right" label="操作" min-width="200" align="center">
          <template slot-scope="scope">
            <span v-if="scope.row.showEdit">
                  <el-button type="success" @click="save(scope.row, scope.$index)">保存</el-button>
                  <el-button type="info" @click="cancel(scope.row, scope.$index)">取消</el-button>
            </span>
            <span v-else>
              <el-button type="primary"
                         @click="$set(result.list, scope.$index, {...scope.row, showEdit: true})">编辑</el-button>
              <el-button type="danger" @click="remove(scope.row.id)">删除</el-button>
            </span>
          </template>
        </el-table-column>
      </el-table>
    </el-card>

    <el-dialog
      v-if="showDialog"
      :title="currentRow.dictName"
      :destroy-on-close="true"
      :visible.sync="showDialog"
      width="80%"
    >
      <dict-data :dict="currentRow"/>
    </el-dialog>
  </div>
</template>

<script>
  import {
    getList,
    save,
    remove
  } from '@/api/system/dict';
  import {deepClone} from "@/utils";
  import dictData from "./components/data";


  export default {
    name: "DictManage",

    components: {
      dictData
    },

    data() {
      return {

        deepClone,

        loading: false,

        queryParams: {
          dictName: '',
          symbol: '',
          remark: '',
          createdAt: [],
          page: 1,
          pageSize: 20,
        },
        pageSizes: [10, 20, 50, 100, 200],
        result: {
          list: [],
          cnt: 0
        },

        defaultRow: {
          showEdit: true,
          id: 0,
          dictName: '',
          symbol: '',
          remark: '',
          createdAt: '',
          updatedAt: '',
        },

        showDialog: false,
        currentRow: {},
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
              item.showEdit = false;
            });
            console.log(this.result.list, 'this.result.list');
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

      cancel(row, index) {
        row.showEdit = false;
        if (row.id > 0) {
          this.$set(this.result.list, index, row);
        } else {
          this.result.list.splice(index, 1);
        }
      },
      async save(row, index) {
        this.loading = true;
        save(row).then((res) => {
          if (res.code > 0) {
            this.$message.error(res.msg)
          } else {
            this.$message.success('操作成功');
            res.data.showEdit = false;
            this.$set(this.result.list, index, res.data);
          }
          this.loading = false
        }).catch((e) => {
          this.loading = false
        })
      },

      showData(row) {
        this.currentRow = row;
        this.showDialog = true;
      },

      async remove(id) {
        this.$confirm('此操作将永久删除该字典, 是否继续?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          this.loading = true;
          remove({id}).then((res) => {
            if (res.code > 0) {
              this.$message.error(res.msg)
            } else {
              this.$message.success('操作成功');
              this.getList();
            }
            this.loading = false
          }).catch((e) => {
            this.loading = false
          });
          this.loading = false;
        }).catch(() => {
          this.$message({
            type: 'info',
            message: '已取消删除'
          });
        });
      },
    }
  }
</script>

<style scoped lang="scss">
  .app-container {
    .form-item-width {
      width: 200px
    }
  }
</style>

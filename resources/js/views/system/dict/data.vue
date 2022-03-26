<template>
  <div class="app-container">
    <el-card>
      <div slot="header">
        {{ $route.query.dictName }}
      </div>
      <el-table
        :data="result"
        border
        height="600px"
      >
        <el-table-column type="" prop="id" width="100" align="center" label="数据ID"/>
        <el-table-column prop="label" width="150" align="center" label="数据标签">
          <template slot-scope="scope">
            <el-input v-if="scope.row.showEdit" v-model="scope.row.label"/>
            <span v-else> {{ scope.row.label }} </span>
          </template>
        </el-table-column>
        <el-table-column prop="value" min-width="100" align="center" label="数据值">
          <template slot-scope="scope">
            <el-input v-if="scope.row.showEdit" v-model="scope.row.value"/>
            <span v-else> {{ scope.row.value }} </span>
          </template>
        </el-table-column>
        <el-table-column prop="sort" min-width="100" align="center" label="排序">
          <template slot-scope="scope">
            <el-input v-if="scope.row.showEdit" v-model="scope.row.sort"/>
            <span v-else> {{ scope.row.sort }} </span>
          </template>
        </el-table-column>
        <el-table-column prop="remark" min-width="200" align="left" label="备注">
          <template slot-scope="scope">
            <el-input v-if="scope.row.showEdit" v-model="scope.row.remark" type="textarea" autosize/>
            <span v-else> {{ scope.row.remark }} </span>
          </template>
        </el-table-column>
        <el-table-column prop="createdAt" min-width="160" align="center" label="创建时间"/>
        <el-table-column prop="updatedAt" min-width="160" align="center" label="更新时间"/>
        <el-table-column fixed="right" label="操作" min-width="200" align="center">
          <template slot="header">
            <el-button
              type="primary"
              @click="result.unshift(deepClone(defaultRow))"
            >
              新增
            </el-button>
          </template>
          <template slot-scope="scope">
            <span v-if="scope.row.showEdit">
                  <el-button type="success" @click="save(scope.row, scope.$index)">保存</el-button>
                  <el-button type="info" @click="cancel(scope.row, scope.$index)">取消</el-button>
            </span>
            <el-button v-else type="primary" @click="$set(result, scope.$index, {...scope.row, showEdit: true})">编辑</el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>
  </div>
</template>

<script>
  import {
    getDataList,
    saveData
  } from '@/api/system/dict';
  import {deepClone} from "@/utils";

  export default {
    name: "DictDataManage",

    components: {},

    data() {
      return {

        deepClone,

        loading: false,

        symbol: '',

        result: [],

        defaultRow: {
          showEdit: true,
          id: 0,
          label: '',
          value: '',
          sort: 1,
          createdAt: '',
          updatedAt: '',
        }
      }
    },

    computed: {},

    async created() {
      this.symbol =  this.$route.query.symbol;
      await this.queryList();
    },

    methods: {
      async queryList() {
        this.getList();
      },

      getList() {
        this.loading = true;
        getDataList({symbol: this.symbol}).then((res) => {
          if (res.code > 0) {
            this.$message.error(res.msg)
          } else {
            this.result = res.data;
            this.result.forEach(item => {
              item.showEdit = false;
            });
            console.log(this.result, 'this.result1');
          }
          this.loading = false
        }).catch((e) => {
          this.loading = false
        })
      },

      cancel(row, index) {
        row.showEdit = false;
        if (row.id > 0) {
          this.$set(this.result, index, row);
        } else {
          this.result.splice(index, 1);
        }
      },
      async save(row, index) {
        this.loading = true;
        row.dictId = this.$route.query.dictId;
        saveData(row).then((res) => {
          if (res.code > 0) {
            this.$message.error(res.msg)
          } else {
            this.$message.success('操作成功');
            res.data.showEdit = false;
            this.$set(this.result, index, res.data);
          }
          this.loading = false
        }).catch((e) => {
          this.loading = false
        })
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

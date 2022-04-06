<template>
  <el-card>
    <el-table
      :data="result"
      border
    >
      <el-table-column type="" prop="id" width="100" align="center" label="数据ID"/>
      <el-table-column prop="label" width="300" align="center" label="数据标签">
        <template slot-scope="scope">
          <el-input v-if="scope.row.showEdit" v-model="scope.row.label"/>
          <span v-else> {{ scope.row.label }} </span>
        </template>
      </el-table-column>
      <el-table-column prop="value" width="300" align="center" label="数据值">
        <template slot-scope="scope">
          <el-input v-if="scope.row.showEdit" v-model="scope.row.value"/>
          <span v-else> {{ scope.row.value }} </span>
        </template>
      </el-table-column>
      <el-table-column prop="sort" width="100" align="center" label="排序">
        <template slot-scope="scope">
          <el-input v-if="scope.row.showEdit" v-model="scope.row.sort"/>
          <span v-else> {{ scope.row.sort }} </span>
        </template>
      </el-table-column>
      <el-table-column prop="remark" width="300" align="left" label="备注">
        <template slot-scope="scope">
          <el-input v-if="scope.row.showEdit" v-model="scope.row.remark" type="textarea" autosize/>
          <span v-else> {{ scope.row.remark }} </span>
        </template>
      </el-table-column>
      <el-table-column prop="createdAt" width="160" align="center" label="创建时间"/>
      <el-table-column prop="updatedAt" width="160" align="center" label="更新时间"/>
      <el-table-column fixed="right" label="操作" width="200" align="center">
        <template slot="header">
          操作
          <el-button
            v-permission="[BTN_DICT_DATA_ADD]"
            type="text"
            class="el-icon-plus"
            style="margin-left: 20px;"
            @click="result.unshift(deepClone(defaultRow))"
          >
            {{ BTN_MAP_DICT[BTN_DICT_DATA_ADD] }}
          </el-button>
        </template>
        <template slot-scope="scope">
            <span v-if="scope.row.showEdit">
                  <el-button type="success" @click="save(scope.row, scope.$index)">保存</el-button>
                  <el-button type="info" @click="cancel(scope.row, scope.$index)">取消</el-button>
            </span>
          <span v-else-if="!scope.row.disable">
            <el-button v-permission="[BTN_DICT_DATA_EDIT]" type="text" icon="el-icon-edit"
                       @click="$set(result, scope.$index, {...scope.row, showEdit: true})">
              {{ BTN_MAP_DICT[BTN_DICT_DATA_EDIT] }}
            </el-button>
            <el-button v-permission="[BTN_DICT_DATA_DEL]" type="text" icon="el-icon-delete" style="color:#F56C6C;"
                       @click="remove(scope.row.id)">
              {{ BTN_MAP_DICT[BTN_DICT_DATA_DEL] }}
            </el-button>
          </span>
        </template>
      </el-table-column>
    </el-table>
  </el-card>
</template>

<script>
  import {
    getDataList,
    addData,
    editData,
    removeData
  } from '@/api/system/dict';
  import {
    BTN_MAP_DICT,
    BTN_DICT_DATA_ADD,
    BTN_DICT_DATA_EDIT,
    BTN_DICT_DATA_DEL,
  } from "../../../../api/btn";
  import {deepClone} from "@/utils";

  export default {
    name: "DictDataManage",

    props: {
      dict: {
        type: Object,
        default: {
          id: 0,
          symbol: '',
          dictName: '',
        }
      },
    },

    components: {},

    data() {
      return {
        BTN_MAP_DICT,
        BTN_DICT_DATA_ADD,
        BTN_DICT_DATA_EDIT,
        BTN_DICT_DATA_DEL,
        deepClone,

        loading: false,

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
      await this.getList();
    },

    methods: {
      getList() {
        this.loading = true;
        getDataList({symbol: this.dict.symbol}).then((res) => {
          if (res.code > 0) {
            this.$message.error(res.msg)
          } else {
            this.result = res.data;
            this.result.forEach(item => {
              item.showEdit = false;
            });
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
        row.dictId = this.dict.id;
        const func = row.id > 0 ? editData : addData;
        func(row).then((res) => {
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
      async remove(id) {
        this.$confirm('此操作将永久删除该数据, 是否继续?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          this.loading = true;
          removeData({dictId: this.dict.id, id: id}).then((res) => {
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

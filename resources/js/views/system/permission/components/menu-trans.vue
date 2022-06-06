<template>
  <div>
    <el-table :data="list" border>
      <el-table-column align="center" label="菜单" prop="defaultTitle" />
      <el-table-column align="center" label="语言" prop="locale">
        <template v-slot="scope">
          <el-select v-if="scope.row.showEdit" v-model="scope.row.locale"  placeholder="请选择语言" style="width:120px;">
            <el-option
              v-for="(lang, key) in languages"
              :key="key"
              :label="lang"
              :value="lang">
            </el-option>
          </el-select>
          <span v-else> {{ scope.row.locale }} </span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="Title" prop="title">
        <template v-slot="scope">
          <el-input v-if="scope.row.showEdit" v-model="scope.row.title" type="text"/>
          <span v-else> {{ scope.row.title }} </span>
        </template>
      </el-table-column>
      <el-table-column align="center" fixed="right" label="操作" width="200">
        <template slot="header">
          <el-button
            type="text"
            class="el-icon-plus"
            @click="list.unshift(deepClone(defaultRow))"
          >
            新增
          </el-button>
        </template>
        <template v-slot="scope">
            <span v-if="scope.row.showEdit">
                  <el-button type="success" @click="trans(scope.row, scope.$index)">保存</el-button>
                  <el-button type="info" @click="cancel(scope.row, scope.$index)">取消</el-button>
            </span>
          <span v-else-if="!scope.row.disable">
              <el-button icon="el-icon-edit" type="text"
                         @click="$set(list, scope.$index, {...scope.row, showEdit: true})">
                编辑
              </el-button>
            </span>
        </template>
      </el-table-column>
    </el-table>

  </div>
</template>

<script>
  import {
    trans,
    getTransList,
  } from '@/api/system/menu';
  import {deepClone} from "@/utils";

  export default {
    name: "",
    props: {
      menuId: {
        type: Number,
        default: 0
      },
      title: {
        type: String,
        default: ""
      },
    },
    data() {
      return {
        deepClone,

        loading: false,
        languages: [
          'zh',
          'en',
          'zh_TW'
        ],
        list: [],
        defaultRow: {
          showEdit: true,
          id: this.menuId,
          defaultTitle: this.title,
          locale: '',
          title: '',
        },
      }
    },
    created() {
      this.getList();
    },
    methods: {
      async getList() {
        const ret = await getTransList({ids: [this.menuId]});
        this.list = ret?.data;
        this.list.forEach(item => {
          item.showEdit = false;
        });
      },

      async trans(row, index) {
        this.loading = true;
        trans(row).then((res) => {
          if (res.code > 0) {
            this.$message.error(res.msg)
          } else {
            this.$message.success('操作成功');
            row.showEdit = false;
            this.$set(this.list, index, row);
          }
          this.loading = false
        }).catch((e) => {
          this.loading = false
        })
      },
      cancel(row, index) {
        row.showEdit = false;
        if (row.id > 0) {
          this.$set(this.list, index, row);
        } else {
          this.list.splice(index, 1);
        }
      },
    }
  }
</script>

<style scoped>

</style>

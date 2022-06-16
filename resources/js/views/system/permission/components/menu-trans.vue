<template>
  <div>
    <el-form  ref="transForm">
      <el-form-item label=" " prop="title">
        <el-input v-model="title" placeholder="请输入" style="width: 100%"/>
      </el-form-item>
    </el-form>
    <div slot="footer">
      <el-button type="primary" @click="trans"> 保存</el-button>
      <el-button type="info" @click="cancel">取消</el-button>
    </div>
  </div>
</template>

<script>
  import {
    trans,
  } from '@/api/system/menu';
  import {locale} from "../../../../settings";

  export default {
    name: "",
    props: {
      transRow: {
        type: Object,
        default: {}
      },
      locale: {
        type: String,
        default: ""
      }
    },
    data() {
      return {
        loading: false,
        title: this.transRow.trans[this.locale]
      }
    },
    methods: {
      async trans() {
        this.loading = true;
        const params = {
          id: this.transRow.id,
          locale: this.locale,
          title: this.title
        }
        trans(params).then((res) => {
          if (res.code > 0) {
            this.$message.error(res.msg)
          } else {
            this.$message.success('操作成功');
            this.transRow.trans[locale] = this.title;
            this.$emit('closeTrans', this.transRow)
          }
          this.loading = false
        }).catch((e) => {
          this.loading = false
        })
      },
      cancel(){
        this.$emit('closeTrans', this.transRow)
      }
    }
  }
</script>

<style scoped>

</style>

<template>
  <div style="cursor: pointer;" @click="click">
    <svg-icon icon-class="notify1"/>
    <el-badge :value="total" class="notify-badge">
    </el-badge>
  </div>
</template>

<script>
  import {getMessage} from "@/api/message";

  export default {
    name: 'Notify',
    data() {
      return {
        total: 0,
        list: [],
      }
    },
    async created() {
      const ret = await getMessage({type: 1});
      this.total = ret?.data?.cnt;
      this.list = ret?.data?.list;
    },
    methods: {
      click() {
        this.$router.push('/notify')
      },
    }
  }
</script>

<style scoped>
  .notify-badge {
    margin-top: -10px;
    margin-left: -10px;
  }
</style>

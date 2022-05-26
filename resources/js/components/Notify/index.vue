<template>
  <div style="cursor: pointer;" @click="Btn_Test_Click">
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
      this.Btn_Test_Click();
    },
    methods: {
      click() {
        this.$router.push('/notify')
      },
      Btn_Test_Click() {

        const param = {
          event: 'notify',
          data: {},
        }
        console.log(JSON.stringify(param), 'JSON.stringify(param)')
        //发送消息
        this.$WebSocket.WebSocketHandle.send(JSON.stringify(param))
      }
    }
  }
</script>

<style scoped>
  .notify-badge {
    margin-top: -10px;
    margin-left: -10px;
  }
</style>

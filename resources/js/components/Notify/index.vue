<template>
  <div style="cursor: pointer;" @click="click">
    <svg-icon icon-class="notify1"/>
    <el-badge v-if="total>0" :value="total" class="notify-badge">
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
      this.$WebSocket.WebSocketBandMsgReceivedEvent(this.WebSocket_OnMessage)
    },
    methods: {
      click() {
        if (this.$route.path !== '/notify/index') {
          this.$router.push("/notify");
        }
      },
      WebSocket_OnMessage(msg) {
        const ret = JSON.parse(msg.data);
        if(ret.event === 'notify-refresh') {
           const data = JSON.parse(ret.data);
          if (data.type === 'incr') {
            this.total += 1;
          }
          console.log('ret.data.type', data.type);
          if (data.type === 'decr') {
            this.total -= 1;
            console.log('total', this.total);
          }
        }
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

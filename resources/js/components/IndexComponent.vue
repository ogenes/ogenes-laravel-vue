<template>
  <div id="app">
    <router-view />
  </div>
</template>

<script>
  import {getToken} from '@/utils/auth'

  export default {
    name: 'App',

    async created() {
      this.$i18n.locale = this.$store.state.settings.locale;
      await this.WebSocketINI();
      // 因为我的页面有缓存机制，用户下次有可能直接打开某个登录后才能访问的页面 比如F5刷新了某个页面 需要重连
      // 又比如后端服务器因为什么原因突然中断了一下 也需要重新连接WebSocket
      // 每3秒检测一次websocket连接状态 未连接 则尝试连接 尽量保证网站启动的时候 WebSocket都能正常长连接
      setInterval(this.WebSocket_StatusCheck, 3000)
    },
    methods: {


      // 1、WebSocket连接状态检测：
      WebSocket_StatusCheck() {

        if (!this.$WebSocket.WebSocketHandle || this.$WebSocket.WebSocketHandle.readyState !== 1) {

          console.log('Websocket连接中断，尝试重新连接:')
          this.WebSocketINI()
        }
      },

      // 2、WebSocket初始化：
      async WebSocketINI() {

        // 1、浏览器是否支持WebSocket检测
        if (!('WebSocket' in window)) {

          console.log('您的浏览器不支持WebSocket!')
          return
        }

        const queryStr = `Authorization=`+getToken() + `&locale=` + this.$store.state.settings.locale;
        const tmpWebsocketServerAddress = 'ws://permission.dev.com:8888?'+ queryStr;//可以直接赋值如：ws://127.0.0.1:1234

        // 3、创建Websocket连接
        const tmpWebsocket = new WebSocket(tmpWebsocketServerAddress)

        // 4、全局保存WebSocket操作句柄：main.js 全局引用
        this.$WebSocket.WebsocketINI(tmpWebsocket)

        // 5、WebSocket连接成功提示
        tmpWebsocket.onopen = function() {

          console.log('websocket 连接成功')
        }

        //6、连接失败提示
        tmpWebsocket.onclose = function(e) {

          console.log('websocket 连接关闭：', e)
        }
      }

    }
  }
</script>

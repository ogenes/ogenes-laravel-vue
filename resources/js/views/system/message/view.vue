<template>
  <div style="width: 80%; margin: 20px auto;">
    <div class="message-list-content">
      <div style="font-weight: bold; font-size: 16px; height: 34px; line-height: 34px;">
        {{ message.title }}
      </div>
      <div style="height: 30px; line-height: 60px;font-size: 14px;">
        <div style="float: left; margin-right: 15px; color: #409EFF;">
          <svg-icon icon-class="cat"/>
          <span>{{ message.cat }}</span>
        </div>
        <div style="float: left; margin-right: 15px;">
          <i class="el-icon-user"/>
          <span> {{ message.publisher }}</span>
        </div>
        <div style="float: left;margin-right: 15px;">
          <svg-icon icon-class="timer"/>
          {{ message.publishTime }}
        </div>
      </div>
    </div>
    <div style="margin-top: 50px;">
      <pre v-html="message.text"/>
    </div>
  </div>
</template>

<script>
  import {getDetail} from '@/api/system/message'

  export default {
    name: "MessageView",
    data() {
      return {
        message: {}
      }
    },

    async created() {
      const id = this.$route.params && this.$route.params.id
      const res = await getDetail({id})
      this.message = res.data
      const title = '查看消息'
      const route = Object.assign({}, this.tempRoute, {title: `${title}-${this.message.id}`})
      await this.$store.dispatch('tagsView/updateVisitedView', route)
      document.title = `${title} - ${this.message.id}`

      console.log(this.message)
    },
  }
</script>

<style scoped>

</style>

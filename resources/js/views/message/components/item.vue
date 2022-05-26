<template>
  <div>
    <el-tabs tab-position="left" :stretch="true" :lazy="true">
      <el-tab-pane
        v-for="(value, key) in catMap"
        :key="key"
        :label="value"
        :lazy="true"
      >
        <list :type-id="typeId" :cat-id="key"/>
      </el-tab-pane>

    </el-tabs>
  </div>
</template>

<script>
  import list from './list';
  import {getOptions} from "@/api/message";

  export default {
    name: "MessageItem",
    props: {
      typeId: {
        type: Number
      },
    },
    components: {
      list,
    },
    computed: {},
    async created() {
      const ret = await getOptions();
      this.catMap = ret?.data?.catMap || [];
      this.catMap[0] = '全部消息';
    },
    data() {
      return {
        catMap: [],
      }
    }
  }
</script>

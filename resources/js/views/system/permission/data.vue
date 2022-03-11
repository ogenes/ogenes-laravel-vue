<template>
  <div class="app-container">
    <div>
      <el-tabs tab-position="top">
        <el-tab-pane
          v-for="(item, key) in options.system"
          :key="key"
          :label="item"
          :lazy="true"
        >
          <data-permission-tab :system-id="key" :system="item" :menu-type-option="options.menuTypeOption"/>
        </el-tab-pane>
      </el-tabs>
    </div>
  </div>
</template>

<script>
  import {
    getOptions,
  } from '@/api/system/menu';
  import dataPermissionTab from "../components/data-permission-tab";


  export default {
    name: "DataPermissionManage",

    components: {
      dataPermissionTab
    },
    data() {
      return {
        loading: false,
        systemId: 1,
        options: [],
      }
    },

    computed: {},

    async created() {
      await this.getOptions();
    },

    methods: {
      async getOptions() {
        this.loading = true;
        const ret = await getOptions();
        this.options = ret?.data || [];
        this.loading = false;
      },
    }
  }
</script>

<style scoped>

</style>

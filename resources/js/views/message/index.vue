<template>
  <div class="app-container">
    <div>
      <el-tabs tab-position="top" type="border-card">
        <el-tab-pane label="未读" :lazy="true">
          <item :type-id="1"/>
        </el-tab-pane>
        <el-tab-pane label="已读" :lazy="true">
          <item :type-id="2"/>
        </el-tab-pane>
      </el-tabs>
    </div>
  </div>
</template>

<script>
  import item from "./components/item";

  export default {
    name: "Message",

    components: {
      item
    },
  }
</script>

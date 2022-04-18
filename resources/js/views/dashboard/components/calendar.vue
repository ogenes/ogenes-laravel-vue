<template>
  <el-calendar v-model="value" />
</template>

<script>
  export default {
    name: "HomeCalendar",
    data() {
      return {
        value: new Date()
      }
    }
  }
</script>

<style scoped>
  .is-selected {
    color: #1989FA;
  }
</style>

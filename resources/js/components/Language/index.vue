<template>
  <el-select v-model="locale" placeholder="请选择语言" style="width:120px;">
    <el-option
      v-for="(item, key) in languages"
      :key="key"
      :label="item"
      :value="key">
    </el-option>
  </el-select>
</template>

<script>
  export default {
    name: "Language",
    data() {
      return {
        languages: {
          'zh': '中文',
          'en': 'English',
          'zh-tw': '中文-繁体'
        },
      }
    },
    computed: {
      locale: {
        get() {
          const val = this.$store.state.settings.locale;
          this.$i18n.locale = val;
          return val;
        },
        set(val) {
          this.$store.dispatch('settings/changeSetting', {
            key: 'locale',
            value: val
          })
        }
      },
    },
  }
</script>

<style scoped>

</style>

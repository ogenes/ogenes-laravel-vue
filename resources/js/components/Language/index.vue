<template>
  <el-select v-model="locale" placeholder="请选择语言" style="width:120px;">
    <el-option
      v-for="(lang, key) in languages"
      :key="key"
      :label="lang"
      :value="lang">
    </el-option>
  </el-select>
</template>

<script>
  export default {
    name: "Language",
    data() {
      return {
        languages: [
          'zh',
          'en',
          'zh_TW'
        ],
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
          location.reload();
        }
      },
    },
  }
</script>

<style scoped>

</style>

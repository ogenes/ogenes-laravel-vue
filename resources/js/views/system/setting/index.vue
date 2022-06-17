<template>
  <div>
    <el-card class="setting-container">
      <div>
        <h4>模板设置</h4>
      </div>
      <div class="setting-item">
        <span>Open Tags-View</span>
        <el-switch v-model="tagsView" class="setting-switch" />
      </div>

      <div class="setting-item">
        <span>Fixed Header</span>
        <el-switch v-model="fixedHeader" class="setting-switch" />
      </div>

      <div class="setting-item">
        <span>Sidebar Logo</span>
        <el-switch v-model="sidebarLogo" class="setting-switch" />
      </div>

    </el-card>
  </div>
</template>

<script>

  export default {
    data() {
      return {}
    },
    computed: {
      fixedHeader: {
        get() {
          if (typeof this.$store.state.settings.fixedHeader === 'string') {
            return this.$store.state.settings.fixedHeader === 'true';
          } else {
            return this.$store.state.settings.fixedHeader;
          }
        },
        set(val) {
          this.$store.dispatch('settings/changeSetting', {
            key: 'fixedHeader',
            value: val
          })
        }
      },
      tagsView: {
        get() {
          if (typeof this.$store.state.settings.tagsView === 'string') {
            return this.$store.state.settings.tagsView === 'true';
          } else {
            return this.$store.state.settings.tagsView;
          }
        },
        set(val) {
          this.$store.dispatch('settings/changeSetting', {
            key: 'tagsView',
            value: val
          })
        }
      },
      sidebarLogo: {
        get() {
          if (typeof this.$store.state.settings.sidebarLogo === 'string') {
            return this.$store.state.settings.sidebarLogo === 'true';
          } else {
            return this.$store.state.settings.sidebarLogo;
          }
        },
        set(val) {
          this.$store.dispatch('settings/changeSetting', {
            key: 'sidebarLogo',
            value: val
          })
        }
      },
    },
    methods: {
    }
  }
</script>

<style lang="scss" scoped>
  .setting-container {
    padding-left: 24px;
    font-size: 14px;
    line-height: 1.5;
    word-wrap: break-word;

    .setting-item {
      color: rgba(0, 0, 0, .65);
      font-size: 14px;
      padding: 12px 0;
      width: 30%;
      border-bottom: 1px solid #000000;
    }

    .setting-switch {
      float: right
    }
  }
</style>

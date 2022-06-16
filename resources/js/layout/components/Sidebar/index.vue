<template>
  <div :class="{'has-logo':showLogo}">
    <logo v-if="showLogo" :collapse="isCollapse"/>
    <el-scrollbar wrap-class="scrollbar-wrapper">
      <el-menu
        :active-text-color="variables.menuActiveText"
        :background-color="variables.menuBg"
        :collapse="isCollapse"
        :collapse-transition="false"
        :default-active="activeMenu"
        :text-color="variables.menuText"
        :unique-opened="false"
        mode="vertical"
      >
        <sidebar-item v-for="route in permission_routes" :key="route.path" :base-path="route.path" :item="route"/>
      </el-menu>
    </el-scrollbar>
  </div>
</template>

<script>
  import {mapGetters} from 'vuex'
  import store from '@/store'
  import Logo from './Logo'
  import SidebarItem from './SidebarItem'
  import variables from '@/styles/variables.module.scss'

  export default {
    components: {SidebarItem, Logo},
    computed: {
      ...mapGetters([
        'permission_routes',
        'sidebar'
      ]),
      activeMenu() {
        const route = this.$route
        const {meta, path} = route
        // if set path, the sidebar will highlight the path you set
        if (meta.activeMenu) {
          return meta.activeMenu
        }
        return path
      },
      showLogo() {
        return this.$store.state.settings.sidebarLogo
      },
      variables() {
        return variables
      },
      isCollapse() {
        return !this.sidebar.opened
      }
    },
    data() {
      return {
        routes: [],
      }
    },
    created() {
      this.transRoutes(this.permission_routes);
    },
    methods: {
      transRoutes(routes) {
        const res = [];
        routes.forEach(item => {
          const map = store.getters.menuMap[item?.name || ''] || {};
          if (Object.keys(map).length > 0) {
            item.meta = {
              ...item.meta,
              title: this.transTitle(item, map),
            };
          }
          if (item.children) {
            item.children = this.transRoutes(item.children)
          }
          res.push(item)
        })
        return res;
      },
      transTitle(route, map) {
        if (Object.keys(map).length > 0) {
          route.title = map.title;
          if (typeof map.trans === 'object') {
            const locale = store.state.settings.locale;
            route.title = map.trans[locale]
          }
        }
        return route.title;
      },

    }
  }
</script>

import { asyncRoutes, constantRoutes } from '@/router'
import store from '@/store'

/**
 * Use meta.role to determine if the current user has permission
 * @param roles
 * @param route
 */
function hasPermission(roles, route) {
  if (!roles.includes('admin') && route.meta && route.meta.roles) {
    return roles.some(role => route.meta.roles.includes(role))
  } else {
    return true
  }
}

/**
 * Filter asynchronous routing tables by recursion
 * @param routes asyncRoutes
 * @param roles
 */
export function filterAsyncRoutes(routes, roles) {
  const res = []

  routes.forEach(route => {
    const tmp = { ...route };
    const map = store.getters.menuMap[tmp?.name || ''] || {};
    const locale = store.state.settings.locale;
    if (Object.keys(map).length > 0) {
      let title = map.title;
      if (typeof map.trans === 'object') {
        title = map.trans[locale]
      }
      tmp.meta = {
        ...tmp.meta,
        title: title,
        icon: map.icon,
        roles: map.roles,
      };
    }
    if (tmp.children) {
      tmp.children = filterAsyncRoutes(tmp.children, roles)
    }
    if (hasPermission(roles, tmp)) {
      res.push(tmp)
    }
  });

  return res
}

const state = {
  routes: [],
  addRoutes: []
};

const mutations = {
  SET_ROUTES: (state, routes) => {
    state.addRoutes = routes;
    state.routes = constantRoutes.concat(routes)
  }
};

const actions = {
  generateRoutes({ commit }, roles) {
    return new Promise(resolve => {
      let accessedRoutes;
      accessedRoutes = filterAsyncRoutes(asyncRoutes, roles);
      commit('SET_ROUTES', accessedRoutes);
      resolve(accessedRoutes);
    })
  }
};

export default {
  namespaced: true,
  state,
  mutations,
  actions
}

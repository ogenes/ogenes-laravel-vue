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
    if (Object.keys(map).length > 0) {
      tmp.meta = {
        ...tmp.meta,
        title: map.title,
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
    state.routes = transRoutes(constantRoutes.concat(routes))
  }
};

function transRoutes(routes) {
  const res = [];
  routes.forEach(item => {
    const map = store.getters.menuMap[item?.name || ''] || {};
    if (Object.keys(map).length > 0) {
      item.meta = {
        ...item.meta,
        title: transTitle(item, map),
      };
    }
    if (item.children) {
      item.children = transRoutes(item.children)
    }
    res.push(item)
  })
  return res;
}
function transTitle(route, map) {
  if (Object.keys(map).length > 0) {
    route.title = map.title;
    if (typeof map.trans === 'object') {
      const locale = store.state.settings.locale;
      route.title = map.trans[locale]
    }
  }
  return route.title;
}

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

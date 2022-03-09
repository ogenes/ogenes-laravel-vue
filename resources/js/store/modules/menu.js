import {getMenuMap} from '@/api/system/menu';

const state = {
  menuMap: {},
};

const mutations = {
  SET_MENU_MAP: (state, menuMap) => {
    state.menuMap = menuMap
  },
};

const actions = {
  genMenuMap({ commit }) {
    return new Promise((resolve, reject) => {
      getMenuMap({ systemId: 1 }).then(response => {
        const { data } = response;
        commit('SET_MENU_MAP', data);
        resolve()
      }).catch(error => {
        reject(error)
      })
    })
  },
};

export default {
  namespaced: true,
  state,
  mutations,
  actions
}

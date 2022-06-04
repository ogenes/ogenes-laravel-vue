import variables from '@/styles/element-variables.module.scss'
import defaultSettings from '@/settings'

const { tagsView, fixedHeader, sidebarLogo, locale } = defaultSettings;

function getItem(key, def) {
  return localStorage.getItem(key) === null ? def : localStorage.getItem(key);
}

function setItem(key, value) {
  localStorage.setItem(key, value);
}

const state = {
  theme: variables.theme,
  tagsView: getItem('tagsView', tagsView),
  fixedHeader: getItem('fixedHeader', fixedHeader),
  sidebarLogo: getItem('sidebarLogo', sidebarLogo),
  locale: getItem('locale', locale),
};

const mutations = {
  CHANGE_SETTING: (state, { key, value }) => {
    if (state.hasOwnProperty(key)) {
      state[key] = value;
      setItem(key, value);
    }
  }
};

const actions = {
  changeSetting({ commit }, data) {
    commit('CHANGE_SETTING', data)
  }
};

export default {
  namespaced: true,
  state,
  mutations,
  actions
}


import variables from '@/styles/element-variables.module.scss'
import defaultSettings from '@/settings'
import { theme } from '@/utils/theme'

const { showSettings, tagsView, fixedHeader, sidebarLogo, darkTheme } = defaultSettings;

function getItem(key, def) {
  const ret = localStorage.getItem(key) === null ? def : localStorage.getItem(key) > 0;

  if (key === 'darkTheme') {
    theme.changeTheme(ret ? 'green' : 'blue');
  }
  return ret;
}

function setItem(key, value) {
  if (typeof value === 'boolean') {
    value = value ? 1 : 0;
  }
  if (key === 'darkTheme') {
    theme.changeTheme(value ? 'green' : 'blue');
  }
  localStorage.setItem(key, value);
}

const state = {
  theme: variables.theme,
  showSettings: getItem('showSettings', showSettings),
  tagsView: getItem('tagsView', tagsView),
  fixedHeader: getItem('fixedHeader', fixedHeader),
  sidebarLogo: getItem('sidebarLogo', sidebarLogo),
  darkTheme: getItem('darkTheme', darkTheme),
};

console.log(state);

const mutations = {
  CHANGE_SETTING: (state, { key, value }) => {
    // eslint-disable-next-line no-prototype-builtins
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


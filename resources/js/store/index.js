import Vue from 'vue';
import Vuex from 'vuex';
import getters from './getters';
import user from './modules/user';
import tagsView from './modules/tagsView';
import app from './modules/app';
import settings from './modules/settings';
import permission from './modules/permission';
import errorLog from './modules/errorLog';

Vue.use(Vuex);

export default new Vuex.Store({
  modules: {
    user,
    tagsView,
    app,
    settings,
    permission,
    errorLog,
  },
  getters,
});

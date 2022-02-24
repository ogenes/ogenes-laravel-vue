import {getToken, setToken, removeToken} from '@/utils/auth';
import {resetRouter} from '@/router'
import axios from "@/utils/axios";
import {syncFiles} from '@/utils/sync';

const state = {
  token: getToken(),
  isUserInfo: false,
  userInfo: {}
};

const mutations = {
  SET_TOKEN: (state, token) => {
    state.token = token
  },
  SET_USER_INFO: (state, userInfo) => {
    state.isUserInfo = Boolean(state.userInfo.email)
    state.userInfo = userInfo
  }
};

const actions = {
  // user login
  login({commit}, userInfo) {
    const {email, password, rememberMe} = userInfo
    return new Promise((resolve, reject) => {
      axios.post('/api/auth/login', {email: email.trim(), password: password}).then((res) => {
        if (res.data.code > 0) {
          reject(res.data.msg)
        } else {
          const token = res.data.data.token
          commit('SET_TOKEN', token);
          commit('SET_USER_INFO', res.data.data.userInfo);
          setToken(token, rememberMe);
          syncFiles()
          resolve()
        }
      }).catch(error => {
        reject(error)
      });
    })
  },

  async getInfo({commit, state}, force = false) {
    if (state.isUserInfo && !force) {
      return Promise.resolve()
    }
    try {
      const res = await axios.post('/api/user/getCurrentUser');
      if (res.data.code > 0) {
        return Promise.reject(res.data.msg)
      } else {
        commit('SET_USER_INFO', res.data.data)
        state.isUserInfo = true
        return Promise.resolve()
      }
    } catch (err) {
      state.isUserInfo = false
      return Promise.reject(err)
    }
  },

  logout({commit}) {
    return new Promise((resolve, reject) => {
      axios.post('/api/auth/logout').then((res) => {
        if (res.data.code > 0) {
          reject(res.data.msg)
        } else {
          commit('SET_TOKEN', '');
          commit('SET_USER_INFO', {});
          removeToken();
          resetRouter();
          resolve()
        }
      }).catch(error => {
        reject(error)
      });
    })
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}


import { login, logout, getInfo } from '@/api/user'
import { getToken, setToken, removeToken } from '@/utils/auth'
import router, { resetRouter } from '@/router'

const state = {
  token: getToken(),
  name: '',
  account: '',
  mobile: '',
  email: '',
  avatar: '',
  introduction: '',
  roles: []
};

const mutations = {
  SET_TOKEN: (state, token) => {
    state.token = token
  },
  SET_INTRODUCTION: (state, introduction) => {
    state.introduction = introduction
  },
  SET_NAME: (state, name) => {
    state.name = name
  },
  SET_ACCOUNT: (state, account) => {
    state.account = account
  },
  SET_MOBILE: (state, mobile) => {
    state.mobile = mobile
  },
  SET_EMAIL: (state, email) => {
    state.email = email
  },
  SET_AVATAR: (state, avatar) => {
    state.avatar = avatar
  },
  SET_ROLES: (state, roles) => {
    state.roles = roles
  }
};

const actions = {
  // user login
  login({ commit }, userInfo) {
    const { account, password, rememberMe } = userInfo
    return new Promise((resolve, reject) => {
      login({ account: account.trim(), password: password }).then(response => {
        const { data } = response;
        commit('SET_TOKEN', data.token);
        setToken(data.token, rememberMe);
        resolve()
      }).catch(error => {
        reject(error)
      })
    })
  },

  // get user info
  getInfo({ commit, state }) {
    return new Promise((resolve, reject) => {
      getInfo().then(response => {
        const { data } = response;

        if (!data) {
          reject('Verification failed, please Login again.')
        }

        let { roles, username, account, mobile, email, avatar, introduction } = data;

        // roles must be a non-empty array
        if (!roles || roles.length <= 0) {
          router.push('/401');
          // reject('没有任何菜单权限，请联系')
        }

        commit('SET_ROLES', roles);
        commit('SET_NAME', username);
        commit('SET_ACCOUNT', account);
        commit('SET_MOBILE', mobile);
        commit('SET_EMAIL', email);
        commit('SET_AVATAR', avatar);
        commit('SET_INTRODUCTION', introduction);
        resolve(data)
      }).catch(error => {
        reject(error)
      })
    })
  },

  // user logout
  logout({ commit, state, dispatch }) {
    return new Promise((resolve, reject) => {
      logout(state.token).then(() => {
        commit('SET_TOKEN', '');
        commit('SET_ROLES', []);
        removeToken();
        resetRouter();

        // reset visited views and cached views
        // to fixed https://github.com/PanJiaChen/vue-element-admin/issues/2485
        dispatch('tagsView/delAllViews', null, { root: true });

        resolve()
      }).catch(error => {
        reject(error)
      })
    })
  },

  // remove token
  resetToken({ commit }) {
    return new Promise(resolve => {
      commit('SET_TOKEN', '');
      commit('SET_ROLES', []);
      removeToken()
      resolve()
    })
  },

  // dynamically modify permissions
  async changeRoles({ commit, dispatch }, role) {
    const token = role + '-token';

    commit('SET_TOKEN', token);
    setToken(token)

    const { roles } = await dispatch('getInfo')

    resetRouter();

    // generate accessible routes map based on roles
    const accessRoutes = await dispatch('permission/generateRoutes', roles, { root: true });
    // dynamically add accessible routes
    router.addRoutes(accessRoutes);

    // reset visited views and cached views
    dispatch('tagsView/delAllViews', null, { root: true })
  }
};

export default {
  namespaced: true,
  state,
  mutations,
  actions
}

// store/index.js

import { createStore } from 'vuex';
import createPersistedState from 'vuex-persistedstate';

export default createStore({
  state: {
    userId: null,
    userStatus: null,
  },
  mutations: {
    setUserId(state, userId) {
      state.userId = userId;
    },
    setUserStatus(state, userStatus) {
      state.userStatus = userStatus;
    },
  },
  actions: {
    login({ commit, state }) {
      // Проверяем, установлены ли уже значения в хранилище
      if (!state.userId || !state.userStatus) {
        const UserId = '0';
        const UserStatus = '0';
        commit('setUserId', UserId);
        commit('setUserStatus', UserStatus);
      }
    },
  },
  getters: {
    getUserId: state => state.userId,
    getUserStatus: state => state.userStatus,
  },
  plugins: [createPersistedState()],
});

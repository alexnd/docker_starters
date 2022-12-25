import Vue from 'vue'
import Vuex from 'vuex'
// TODO use in future
// import createPersistedState from 'vuex-persistedstate'
import getters from './getters'
import mutations from './mutations'
import actions from './actions'

Vue.use(Vuex)

export default new Vuex.Store({
  // modules: {}
  state: {
    modalVisible: false
  },
  getters,
  mutations,
  actions,
  plugins: [
    // createPersistedState({
    //  paths: ['filters', 'payment'],
    // }),
  ]
})

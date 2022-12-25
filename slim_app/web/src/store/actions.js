export default {
  resetFilters({ commit }) {
    commit('resetFilters')
  },

  resetScroll() {
    window.scrollTo(0, 0)
  }
}

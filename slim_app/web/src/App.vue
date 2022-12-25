<template>
  <div id="main">
    <page-header></page-header>
    <div id="content">
      <router-view />
    </div>
    <page-footer></page-footer>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import PageHeader from '@/components/Header'
import PageFooter from '@/components/Footer'

export default {
  name: 'App',
  components: { PageHeader, PageFooter },
  mounted() {
    console.log('*[app mounted]')
  }
}
</script>

<style lang="scss">
#content {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
}

#nav {
  padding: 30px;

  a {
    font-weight: bold;
    color: #2c3e50;

    &.router-link-exact-active {
      color: #42b983;
    }
  }
}
</style>

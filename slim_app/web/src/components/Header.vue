<template>
  <nav class="flex flex-wrap items-center justify-between p-5 bg-blue-200">
    <router-link to="/" title="Return to Base"><b>Home</b></router-link>
    <a
      href="#"
      class="font-bold py-2 px-4 rounded bg-green-200"
      id="btn_play_radio"
    >
      <span id="playbtn_txt">Play Radio &gt;&gt;</span>
    </a>
    <div class="relative pt-1" style="display:none" id="c_player_volume">
      <input
        type="range"
        class="form-range appearance-none w-15 h-1 p-0 bg-gray-100 focus:outline-none focus:ring-0 focus:shadow-none"
        id="player_volume"
      />
    </div>
    <a href="/login" class="font-bold py-2 px-4 rounded bg-gray-200">login</a>
  </nav>
</template>

<script>
export default {
  name: 'PageHeader'
}
</script>

<style scoped>

</style>

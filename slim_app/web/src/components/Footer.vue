<template>
  <footer class="footer bg-white relative pt-1 border-b-2 border-blue-700">
    <div class="container mx-auto px-6">
      <div class="sm:flex sm:mt-8">
        <div class="mt-8 sm:mt-0 sm:w-full sm:px-8 flex flex-col md:flex-row justify-between">
          <div class="flex flex-col">
            <span class="font-bold text-gray-700 uppercase mb-2">Music</span>
            <span class="my-2"><a
              href="#"
              class="text-blue-700 text-md hover:text-blue-500"
            >PLAY EMBEDDED RADIO</a></span>
            <span class="my-2">
                <a
                  href="/radio.pls"
                  class="text-blue-700 text-md hover:text-blue-500"
                  title="Tune in with standalone player"
                >Download playlist file (.pls)</a>
              </span>
            <span class="my-2">
                <a href="/radio.html" class="text-blue-700 text-md hover:text-blue-500">Radio stations</a>
              </span>
          </div>
          <div class="flex flex-col">
            <span class="font-bold text-gray-700 uppercase mt-4 md:mt-0 mb-2">AppName</span>
            <span class="my-2"><a href="/" class="text-blue-700 text-md hover:text-blue-500">Home</a></span>
            <span class="my-2"><a href="/login" class="text-blue-700 text-md hover:text-blue-500">Login</a></span>
            <span class="my-2">
                <router-link to="/about" class="text-blue-700 text-md hover:text-blue-500">About</router-link>
              </span>
            <span class="my-2"><a href="/faq" class="text-blue-700 text-md hover:text-blue-500">Help/FAQ</a></span>
            <span class="my-2">
                <a href="/terms" class="text-blue-700 text-md hover:text-blue-500">Terms of service</a>
              </span>
          </div>
          <div class="flex flex-col">
            <span class="font-bold text-gray-700 uppercase mt-4 md:mt-0 mb-2">Links</span>
            <span class="my-2"><a href="http://zion.ua" class="text-blue-700 text-md hover:text-blue-500">Z-Radio</a></span>
          </div>
        </div>
      </div>
    </div>
    <div class="container mx-auto px-6">
      <div class="mt-16 border-t-2 border-gray-300 flex flex-col items-center">
        <div class="sm:w-2/3 text-center py-6">
          <p class="text-sm text-blue-700 font-bold mb-2">
            &copy; 2006-2021 by <a href="https://alexnd.kiev.ua">[ND]</a>
          </p>
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  name: 'PageFooter'
}
</script>

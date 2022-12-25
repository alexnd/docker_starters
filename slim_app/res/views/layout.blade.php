<!doctype html>
<html lang="en">
<head>
<title>{{ $title }}</title>
<link rel="stylesheet" href="/css/app.css"/>
<script type="text/javascript" src="/js/howler.core.min.js"></script>
<script type="text/javascript" src="/js/app.js"></script>
<meta charset="utf-8"/>
{{--<script async src="https://www.googletagmanager.com/gtag/js?id=G-SHN5W8LY4Y"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', 'G-SHN5W8LY4Y');
</script>--}}
</head>
<body>
<div id="app">
  @if (isset($menu) && !empty($menu))
  <nav class="flex flex-wrap items-center justify-between p-5 bg-blue-200">
    <a href="/" title="Happy DJ's Base;)"><b>{{ $title }}</b></a>
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
    @if (isset($user) && !empty($user))
    <i
      @if (isset($role) && !empty($role))
      title="{{ $role }}"
      @endif
    >Greetings to {{ $user }}!</i>
    <a href="/logout" class="font-bold py-2 px-4 rounded bg-red-200">logout</a>
    @else
    <a href="/login" class="font-bold py-2 px-4 rounded bg-gray-200">login</a>
    @endif
  </nav>
  @endif
  <div id="content">
    <div class="text-center">
      <span id="status_txt" class="text-red-500">TURNED OFF</span>
    </div>
    <div class="flex flex-wrap justify-center items-center">
      <div class="bg-white shadow-md hover:shadow-xl rounded-lg border-2 w-1/2 p-4 mt-10">
        @if (isset($content_title) && !empty($content_title))
        <h3 class="text-6xl text-gray-800 font-semibold">
        {{ $content_title }}
        </h3>
        <hr/>
        @endif
        @yield('content')
      </div>
    </div>
  </div>
  <form
    id="form-login"
    method="post"
    action="/login"
    onsubmit="event.preventDefault();return false"
    style="display:none"
  >
    <div class="mt-8 max-w-md">
      <div class="grid grid-cols-1 gap-6">
        <label class="block">
          <span class="text-gray-700">Login</span>
          <input
            type="text"
            class="mt-1 block w-full rounded-md bg-gray-100 border-transparent focus:border-gray-500 focus:bg-white focus:ring-0"
            placeholder="username"
          >
        </label>
        <label class="block">
          <span class="text-gray-700">Password</span>
          <input
            type="text"
            class="mt-1 block w-full rounded-md bg-gray-100 border-transparent focus:border-gray-500 focus:bg-white focus:ring-0"
            placeholder="********"
          >
        </label>
        <div class="block">
          <div class="mt-2">
            <div>
              <label class="inline-flex items-center">
                <input
                  type="checkbox"
                  checked="true"
                  class="rounded bg-gray-200 border-transparent focus:border-transparent focus:bg-gray-200 text-gray-700 focus:ring-1 focus:ring-offset-2 focus:ring-gray-500"
                >
                <span class="ml-2">Remember login</span>
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
  <footer class="footer bg-white relative pt-1 border-b-2 border-blue-700">
    <div class="container mx-auto px-6">
        <div class="sm:flex sm:mt-8">
            <div class="mt-8 sm:mt-0 sm:w-full sm:px-8 flex flex-col md:flex-row justify-between">
                <div class="flex flex-col">
                    <span class="font-bold text-gray-700 uppercase mb-2">Music</span>
                    <span class="my-2"><a
                      href="#"
                      onclick="window.scrollTo(0,0);return playRadio(event)"
                      class="text-blue-700 text-md hover:text-blue-500"
                      >PLAY EMBEDDED RADIO</a></span>
                    <span class="my-2"><a href="/radio.html" class="text-blue-700 text-md hover:text-blue-500">Selected radio stations</a></span>
                    <span class="font-bold text-gray-700 uppercase mb-2 mt-2">Tune in with standalone player</span>
                    <span class="my-2"><a href="/radio.pls" class="text-blue-700 text-md hover:text-blue-500">Download radio.pls</a></span>
                    <span class="my-2"><a href="/radio.m3u" class="text-blue-700 text-md hover:text-blue-500">Download radio.m3u</a></span>
                </div>
                <div class="flex flex-col">
                    <span class="font-bold text-gray-700 uppercase mt-4 md:mt-0 mb-2">{{ $title }}</span>
                    <span class="my-2"><a href="/" class="text-blue-700 text-md hover:text-blue-500">Home</a></span>
                    <span class="my-2"><a href="/login" class="text-blue-700 text-md hover:text-blue-500">Login</a></span>
                    <span class="my-2"><a href="/about" class="text-blue-700 text-md hover:text-blue-500">About</a></span>
                    <span class="my-2"><a href="/faq" class="text-blue-700 text-md hover:text-blue-500">Help/FAQ</a></span>
                    <span class="my-2"><a href="/terms" class="text-blue-700 text-md hover:text-blue-500">Terms of use</a></span>
                </div>
                <div class="flex flex-col">
                  <span class="font-bold text-gray-700 uppercase mt-4 md:mt-0 mb-2">Links</span>
                  <span class="my-2"><a href="https://github.com/alexnd/docker_starters" class="text-blue-700 text-md hover:text-blue-500">GitHub</a></span>
                </div>
            </div>
        </div>
    </div>
    <div class="container mx-auto px-6">
        <div class="mt-16 border-t-2 border-gray-300 flex flex-col items-center">
            <div class="sm:w-2/3 text-center py-6">
                <p class="text-sm text-blue-700 font-bold mb-2">
                    &copy 2006-2022 by <a href="https://github.com/alexnd">[Aleks ND]</a>
                </p>
            </div>
        </div>
    </div>
  </footer>
</div>
</body>
</html>

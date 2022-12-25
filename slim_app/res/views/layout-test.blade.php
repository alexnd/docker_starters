<?php
$json_data = ['user' => null];
$title = $title ?? '';
?><!doctype html>
<html lang="en">
<head>
  <title>{{ $title }}</title>
  {{-- <link rel="stylesheet" href="/css/app.css"/>
  <script type="text/javascript" src="/js/howler.core.min.js"></script>
  <script type="text/javascript" src="/js/app.js"></script> --}}
  <meta charset="utf-8"/>
</head>
<body>
<div id="app">
@yield('content')
</div>
<div style="display:none">{{ json_encode($json_data) }}</div>
</body>
</html>

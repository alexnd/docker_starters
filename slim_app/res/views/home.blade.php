<?php
$content_title = '';
$menu = true;

$files = [
[
'id' => 1,
'hash' => '298c8addef5aec7f045ee5b44e644f55',
'type' => 'jpg',
'cname' => 'android',
'info' => 'Android',
'size' => 0,
'w' => 96,
'h' => 96
],
[
'id' => 2,
'hash' => '',
'type' => '',
'cname' => '',
'info' => 'Hello World!;)',
'size' => 0,
'w' => 0,
'h' => 0
],
[
'id' => 3,
'hash' => '260d57586012b0ed1ae78accc0bf7083',
'type' => 'png',
'cname' => 'logo',
'info' => 'React',
'size' => 0,
'w' => 512,
'h' => 512
]
];

function isImageType($type) {
  return in_array($type, ['png', 'apng', 'jpg', 'jpeg', 'gif', 'svg', 'bpm', 'avif', 'webp']);
}

function isEditorRole($role) {
  return ($role === 'admin' || $role == 'tester');
}
?>

@extends('layout')

@section('content')
@if (isset($role) && isEditorRole($role))
<a href="/post" class="font-bold py-2 px-4 rounded bg-blue-500 text-white">new</a>
@endif
<div class="blog text-center">
<ul>
  @foreach ($files as $f)
  <li>
    @if (empty($f['hash']))
      <p>{{ $f['info'] }}</p>
    @else
    <a href="/media/{{ $f['hash'] }}.{{ $f['type'] }}">
      @if (isImageType($f['type']))
      <img
        src="/media/{{ $f['hash'] }}.{{ $f['type'] }}"
	@if ($f['w'])
        width="{{ $f['w'] }}"
	@endif
	@if ($f['h'])
        width="{{ $f['h'] }}"
	@endif
	alt="<?php echo $f['info'] ?? $f['cname'];?>"
      />
      @else
      <span><?php echo $f['info'] ?? $f['cname'];?></span>
      @endif
    </a>
    @endif
  </li>
  @endforeach
</ul>
</div>
@if (isset($role) && isEditorRole($role))
<a href="/post" class="font-bold py-2 px-4 rounded bg-blue-500 text-white">new</a>
@endif
<style>
.blog ul li {
margin-bottom: 10px;
border-bottom: 1px solid #ccc;
}
.blog ul li:last-child {
border-bottom: none;
}
.blog ul li img {
margin-left: auto;
margin-right: auto;
}
</style>
@endsection

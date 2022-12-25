<?
$content_title = 'Add Post';
?>

@extends('layout')

@section('content')

<div class="main-content">
  <div class="max-w-xl mx-auto py-12 divide-y md:max-w-4xl">
    <div class="">
      <h2 class="text-2xl font-bold">Add blog post/file</h2>
        <p class="mt-2 text-lg text-gray-600">
          Filenames generated as md5 hashes at /media
        </p>
        <form
          id="form-post"
          method="post"
          enctype="multipart/form-data"
          action="/post"
        >
        <div class="mt-8 max-w-md">
          <div class="grid grid-cols-1 gap-6">
            <label class="block">
              <span class="text-gray-700">Title</span>
              <input
                name="title"
                type="text"
                class="mt-1 block w-full rounded-md bg-gray-100 border-transparent focus:border-gray-500 focus:bg-white focus:ring-0"
                placeholder=""
              >
            </label>
            <label class="block">
              <span class="text-gray-700">Media</span>
              <input
                name="media"
                type="file"
                class="mt-1 block w-full rounded-md bg-gray-100 border-transparent focus:border-gray-500 focus:bg-white focus:ring-0"
              >
            </label>
            <label class="block">
              <span class="text-gray-700">Description</span>
              <textarea
                name="description"
                class="mt-1 block w-full rounded-md bg-gray-100 border-transparent focus:border-gray-500 focus:bg-white focus:ring-0"
                rows="3"
              ></textarea>
            </label>
            <label class="block">
              <span class="text-gray-700">URL</span>
              <input
                name="title"
                type="text"
                class="mt-1 block w-full rounded-md bg-gray-100 border-transparent focus:border-gray-500 focus:bg-white focus:ring-0"
                placeholder=""
              >
            </label>
            <label class="block">
              <span class="text-gray-700">Type of post</span>
              <select
                name="type"
                class="block w-full mt-1 rounded-md bg-gray-100 border-transparent focus:border-gray-500 focus:bg-white focus:ring-0"
              >
                <option value="1">Message</option>
                <option value="2">Download</option>
                <option value="3">Link</option>
                <option value="4">Integration</option>
              </select>
            </label>
            <div class="block">
              <div class="mt-2">
                <div>
                  <label class="inline-flex items-center">
                    <input
                      name="ispost"
                      type="checkbox"
                      class="rounded bg-gray-200 border-transparent focus:border-transparent focus:bg-gray-200 text-gray-700 focus:ring-1 focus:ring-offset-2 focus:ring-gray-500"
                    >
                    <span class="ml-2">Make visible post in blog</span>
                  </label>
                </div>
              </div>
            </div>

            <div class="block">
              <div class="mt-2">
                <div>
                  <input type="submit" name="action" value="Send" class="font-bold py-2 px-4 rounded bg-green-400">
                  &nbsp; &nbsp; &nbsp;
                  <a href="/" class="font-bold py-2 px-4 rounded bg-red-400">Cancel</a>	
		</div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </form>
  </div>
</div>

@endsection

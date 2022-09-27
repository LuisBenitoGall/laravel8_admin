@props(['icon' => '', 'icons' => [], 'id' => false, 'msg' => '', 'mimes_img' => [], 'url' => false])

<div class="dropzone" @if($id) id="{{ $id }}" @endif></div>


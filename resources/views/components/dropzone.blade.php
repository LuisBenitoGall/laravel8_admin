@props(['icon' => '', 'icons' => [], 'id' => false, 'msg' => '', 'mimes_img' => [], 'url' => false])

<div class="dropzone" @if($id) id="{{ $id }}" @endif></div>

@once
@push('styles')
<link rel="stylesheet" href="{{ asset('css/plugins/dropzone/dropzone.css') }}">
@endpush
@endonce
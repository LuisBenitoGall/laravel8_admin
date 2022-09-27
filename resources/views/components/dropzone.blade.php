@props(['icon' => '', 'icons' => [], 'id' => false, 'msg' => '', 'mimes_img' => [], 'url' => false])

<div class="dropzone" @if($id) id="{{ $id }}" @endif></div>

@once
@push('styles')
<link rel="stylesheet" href="{{ asset('css/plugins/dropzone/dropzone.css') }}">
@endpush
@endonce

@once
@push('scripts')
<script src="{{ asset('js/plugins/dropzone/dropzone.js') }}"></script>

<script>
var mimes_img = {!! $mimes_img !!};
var icons = {!! $icons !!};
























</script>
@endpush
@endonce
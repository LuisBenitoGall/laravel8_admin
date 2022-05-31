@props(['icon' => '', 'id' => false, 'msg' => '', 'mimes_img' => [], 'url' => false])

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

//Dropzone imágenes:
var myDropzone = new Dropzone('.dropzone', {
    url: '{{ $url }}',
    paramName: 'file',
    aceptedFiles: 'image/*',
    maxFilesize: 2,     //2MB
    maxFiles: 10,
    headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    dictDefaultMessage: '<i class="fa fa-{{ $icon }}"></i> {{ trans("textos.".$msg) }}'
});

//Envío correcto:
myDropzone.on('success', function(file, res, done){
    //Sin acciones definidas.
});

//Error:
myDropzone.on('error', function(file, res){
    var msg = res.errors.photo[0];
    $('.dz-error-message:last > span').text(msg);
});

Dropzone.autoDiscover = false;
</script>
@endpush
@endonce

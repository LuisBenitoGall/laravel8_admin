@props(['url'])

<div class="dropzone"></div>

@once
@push('styles')
<link rel="stylesheet" href="{{ asset('css/plugins/dropzone/dropzone.css') }}">
@endpush
@endonce

@once
@push('scripts')
<script src="{{ asset('js/plugins/dropzone/dropzone.js') }}"></script>

<script>
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
    dictDefaultMessage: '<i class="fa fa-camera-retro"></i> {{ trans("textos.arrastra_fotos") }}'
});

myDropzone.on('error', function(file, res){
    var msg = res.errors.photo[0];
    $('.dz-error-message:last > span').text(msg);
});

Dropzone.autoDiscover = false;
</script>
@endpush
@endonce

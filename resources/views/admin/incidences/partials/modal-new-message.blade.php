<div class="modal fade modal-new-message" id="modal-new-message" tabindex="-1" role="dialog" aria-labelledby="newMessage" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="newMessage">{!! ucfirst(trans('textos.mensaje_nuevo')) !!} <b></b></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('incidence-messages.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="incidence_id" value="{{ $incidenceId }}">
                    <input type="hidden" name="docs" id="incidence-docs">
                    <input type="hidden" name="response_to" id="responseTo">

                    <div class="form-group row">
                        {{-- Acción correctiva --}}
                        <label for="corrective_action" class="col-md-4 col-lg-3">{!! ucfirst(trans('textos.accion_correctiva')) !!}</label>
                        <div class="col-md-1">
                            <label class="custom-chk-rdb">
                                <input type="checkbox" name="corrective_action">
                                <span class="chk"></span>
                            </label>

                            {!! App\Helpers\Helpers::info('incidence-corrective-action', true) !!}
                        </div>
                    </div>

                    <div class="form-group row">
                    	{{-- Mensaje --}}
                    	<label for="message" class="col-lg-12">{!! ucfirst(trans('textos.mensaje')) !!}*</label>
                        <div class="col-lg-12">
                            <x-input-textarea name="message" class="editor"/>
                        </div>
                    </div>

                    <div class="form-group row">
                    	{{-- Documentos --}}
                    	<label for="docs" class="col-lg-12">{!! ucfirst(trans('textos.documentos')) !!}</label>
                        <div class="col-lg-12">
                    		{{-- @include('admin.partials.dropzone', ['id' => 'incidenceDocs', 'icon' => 'file', 'msg' => 'arrastra_docs', 'url' => '/admin/incidence-docs/'.$incidenceId.'/store']) --}}

                            <div class="dropzone" id="incidenceDocs"></div>
                    	</div>
                    </div>

                    <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                </form>
            </div>
        </div>
    </div>
</div>

@once
@push('styles')
<link rel="stylesheet" href="{{ asset('css/plugins/dropzone/dropzone.css') }}">
@endpush
@endonce

@once
@push('scripts')
<script src="{{ asset('js/plugins/dropzone/dropzone.js') }}"></script>

<script>
// Inicializar la primera instancia de Dropzone
var myDropzone1 = new Dropzone('#incidenceDocs', {
    url: '/admin/incidence-docs/{{ $incidence->id }}/store',
    paramName: 'file',
    acceptedFiles: 'image/*, .pdf',
    maxFilesize: 2, // MB
    maxFiles: 10,
    headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    dictDefaultMessage: '<i class="fa fa-file"></i> {{ trans("textos.arrastra_docs") }}'
});

// Inicializar la segunda instancia de Dropzone
var myDropzone2 = new Dropzone('#incidenceDocs2', {
    url: '/admin/incidence-docs/{{ $incidence->id }}/store',
    paramName: 'file',
    acceptedFiles: 'image/*, .pdf',
    maxFilesize: 2, // MB
    maxFiles: 10,
    headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    dictDefaultMessage: '<i class="fa fa-file"></i> {{ trans("textos.arrastra_docs") }}'
});

// Manejo de eventos para la primera instancia
myDropzone1.on('success', function(file, res) {
    //Incidence Docs:
    if($('#incidenceDocs').length > 0){
        var incidence_docs = $('#incidence-docs').val();
        incidence_docs += ','+res.id;
        $('#incidence-docs').val(incidence_docs);
    }
});

myDropzone1.on('error', function(file, res) {
    var msg = res.errors.photo[0];
    $('.dz-error-message:last > span').text(msg);
});

// Manejo de eventos para la segunda instancia
myDropzone2.on('success', function(file, res) {
    // Manejar la respuesta de éxito
});

myDropzone2.on('error', function(file, res) {
    var msg = res.errors.photo[0];
    $('.dz-error-message:last > span').text(msg);
});

Dropzone.autoDiscover = false; // Desactivar la auto detección de Dropzone

$(function(){
    //Modal nuevo message:
    $('#addMessage').on('click', function(){
        $('#modal-new-message #newMessage').text('{{ trans('textos.mensaje_nuevo') }}');
        $('#responseTo').val('');   
    });

    //Modal respuesta:
    $(document).on('click', '.btnAddResponse', function(){
        $('#modal-new-message #newMessage').text('{{ trans('textos.responder') }}');
        var id = $(this).data('id');
        $('#responseTo').val(id);
    });
});
</script>
@endpush
@endonce
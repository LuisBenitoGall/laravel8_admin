@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Índice --}}
	@include('admin.handbook.partials.menu')

	{{-- Contenidos --}}
	<div class="col-md-9">
		<div class="card mb-3">
			<div class="card-body">	
				<h3 id="handbookTitle"></h3>

				<div id="handbookExcerpt"></div>

				<div id="handbookContent">
					<h2 class="mb-5 mt-5 text-center text-warning">{{ ucfirst(trans('textos.manual_uso')) }}</h2>
				</div>
			</div>
		</div>
	</div>
</div>

@once
@push('styles')
<link rel="stylesheet" href="{{ asset('css/plugins/jsTree/style.min.css') }}">
@endpush
@endonce

@push('scripts')
<script src="{{ asset('js/plugins/jsTree/jstree.min.js') }}"></script>

<script>
$(function(){
	$('#jstree').jstree({
        'core' : {
            'check_callback' : true
        },
        'plugins' : [ 'types', 'dnd' ],
        'types' : {
            'default' : {
                'icon' : 'fa fa-folder'
            },
            'html' : {
                'icon' : 'fa fa-file-code-o'
            },
            'svg' : {
                'icon' : 'fa fa-file-picture-o'
            },
            'css' : {
                'icon' : 'fa fa-file-code-o'
            },
            'img' : {
                'icon' : 'fa fa-file-image-o'
            },
            'js' : {
                'icon' : 'fa fa-file-text-o'
            }
        }
    });

    // Obtener contenido del Handbook.
    $('.getHbContent').on('click', function(e){
        e.preventDefault();
        var code = $(this).data('hb');

        if(code){
            $.get("/admin/handbook/"+code+"/show", function(response, state){
                if(response){
                    $('#handbookTitle').text(response.titulo);
                    $('#handbookExcerpt').html(response.destacado);
                    $('#handbookContent').html(response.contenido);    
                }else{
                    $('#handbookTitle').text('');
                    $('#handbookExcerpt').html('');
                    $('#handbookContent').html('<h3 class="mb-5 mt-5 text-center text-warning">'+txtContenidoPendienteRevision+'</h3>');    
                }
            });
        }
    });

});
</script>
@endpush

@endsection
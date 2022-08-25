@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Recargar página --}}
		<a href="{{ route('import_db.index') }}" class="btn btn-info">
			<i class="fa fa-redo"></i>
			{!! ucfirst(trans('textos.recargar_pagina')) !!}
		</a>

		{{-- Iniciar importación --}}
		<a href="#" class="btn btn-info" id="start">
			<i class="fa fa-file-import"></i>
			{!! ucfirst(trans('textos.importar')) !!}
		</a>
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">

			</div>
		</div>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//var not_intervals = true;
	var prev = '';

	$('#start').on('click', function(e){
        e.preventDefault(); 
        prev = 'usuarios';
        $('.card-body').html('<p>Importando '+prev+'...</p>');

        $.get("/admin/import_db/start", function(response, state){
	        
    	});

    	var interval = setInterval(function(){
    		$.get("/admin/import_db/"+prev+"/messages", function(resp, state){	
    			if(resp){
    				$('.card-body').append('<p>Importación de '+prev+' completada.</p><p>Importando '+resp+'...</p>');
    				prev = resp;	
    			}
    		});
    	}, 1500);
    });
});
</script>
@endpush

@endsection
@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('contracts.index')
			<a href="{{ route('contracts.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_contratos') !!}
			</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('contracts.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Título del contrato --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.contrato')) !!} *</label>
						<div class="col-lg-6">
							<x-input type="text" name="name" id="name" placeholder="{!! ucfirst(trans('textos.contrato')) !!}" autocomplete="off" maxlength="255" required/>
						</div>	

						{{-- Estado --}}
						<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.contrato_activo')) !!}</label>
						<div class="col-md-1">
							<label class="custom-chk-rdb">
                        		<input type="checkbox" name="status" checked>
                        		<span class="chk mt-1"></span>
                    		</label>
                        </div>
					</div>

					<div class="form-group row">
						{{-- Duración --}}
						<label for="duration" class="col-md-2 col-form-label text-md-right">{!! ucfirst(trans('textos.duracion')) !!} <small>({!! trans('textos.dias') !!})</small></label>
                        <div class="col-md-3">
                        	<x-input type="text" name="duration" class="text-right" id="duration" autocomplete="off" maxlength="6"/>
                        </div>

                        {{-- Indefinido --}}
                        <label for="undefined" class="col-md-2 col-form-label text-md-right">{!! ucfirst(trans('textos.es_indefinido')) !!}</label>
                        <div class="col-md-1 position-relative">
							<label class="custom-chk-rdb">
                        		<input type="checkbox" name="undefined" id="undefined">
                        		<span class="chk mt-1"></span>
                    		</label>

                    		<span class="block-chk">
                    			<i class="fa fa-times text-danger"></i>
                    		</span>
                        </div>

                        {{-- Vacaciones --}}
                        <label for="with_holidays" class="col-md-2 col-form-label text-md-right">{!! ucfirst(trans('textos.incluye_vacaciones_question')) !!}</label>
                        <div class="col-md-1">
							<label class="custom-chk-rdb">
                        		<input type="checkbox" name="with_holidays" checked>
                        		<span class="chk mt-1"></span>
                    		</label>
                        </div>
					</div>
					
					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Los contratos indefinidos no tienen número de días:
	$('#duration').on('change', function(){
		var duration = $(this).val();
		if(duration != ''){
			$('#undefined').attr('disabled', true);
			$('.block-chk').fadeIn();
		}else{
			$('#undefined').removeAttr("disabled");	
			$('.block-chk').fadeOut();
		}
	});

	$('#undefined').on('change', function(){
		var status = '';
        if($(this).is(':checked')){
        	$('#duration').attr('disabled', true);   
        }else{
            $('#duration').removeAttr("disabled");	
        }  	
	});
});
</script>
@endpush

@endsection

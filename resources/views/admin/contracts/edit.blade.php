@extends ('admin.partials.layout')

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

		@can('contracts.create')
			<a class="btn btn-info" href="{{ route('contracts.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.contrato_nuevo')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('contracts.update', $contract) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Título del contrato --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.contrato')) !!} *</label>
						<div class="col-lg-6">
							<x-input type="text" name="name" id="name" autocomplete="off" maxlength="255" required value="{{ $contract->name }}"/>
						</div>	

						{{-- Estado --}}
						<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.contrato_activo')) !!}</label>
						<div class="col-md-1">
							<label class="custom-chk-rdb">
                        		<input type="checkbox" name="status" @if($contract->status == 1) checked @endif>
                        		<span class="chk mt-1"></span>
                    		</label>
                        </div>
					</div>

					<div class="form-group row">
						{{-- Duración --}}
						<label for="duration" class="col-md-2 col-form-label text-md-right">{!! ucfirst(trans('textos.duracion')) !!} <small>({!! trans('textos.dias') !!})</small></label>
                        <div class="col-md-3">
                        	@if($contract->duration == config('constants.CONTRATO_INDEFINIDO_DURACION_'))
                        		<x-input type="text" name="duration_" disabled value="{{ ucfirst(trans('textos.indefinido')) }}"/>
                        		<input type="hidden" name="undefined" value="1">
                        	@else
                            	<x-input type="text" name="duration" class="text-right" id="duration" autocomplete="off" maxlength="6" value="{{ $contract->duration }}"/>
                           	@endif
                        </div>

                        {{-- Vacaciones --}}
                        <label for="with_holidays" class="col-md-2 col-form-label text-md-right">{!! ucfirst(trans('textos.incluye_vacaciones_question')) !!}
                        	@if($contract->with_holidays)
                        		<i class="fa fa-check text-success ml-3"></i>
                        	@else
                        		<i class="fa fa-times text-danger ml-3"></i>
                        	@endif
                        </label>
					</div>
					
					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection

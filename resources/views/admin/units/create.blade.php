@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-sm-12">
		@can('units.index')
			<a href="{{ route('units.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_unidades_venta') !!}
			</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-xs-12 col-sm-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('units.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Nombre unidad --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.unidad_venta')) !!} *</label>
						<div class="col-lg-4">
							<x-input type="text" name="name" id="name" placeholder="{!! ucfirst(trans('textos.unidad')) !!}" autocomplete="off" maxlength="150" required/>
						</div>	

						{{-- Tarifa --}}
						<label for="symbol" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.simbolo')) !!}</label>
						<div class="col-lg-1">
							<x-input type="text" name="symbol" id="symbol" autocomplete="off" maxlength="4"/>
						</div>	

						{{-- Status --}}
						<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.activo')) !!}</label>
						<div class="col-lg-1">
							<label class="custom-chk-rdb" rel="tooltip" title="{!! trans('textos.sede_on_off') !!}">
	                            <input type="checkbox" name="status" checked>
	                            <span class="chk" style="top: 5px;"></span>
	                        </label>
						</div>
					</div>

					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection
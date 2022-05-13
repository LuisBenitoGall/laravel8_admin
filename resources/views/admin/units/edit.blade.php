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

		@can('units.create')
			<a class="btn btn-info" href="{{ route('units.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.unidad_nueva')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-xs-12 col-sm-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('units.update', $unit) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}
					<input type="hidden" name="id" value="{{ $unit->id }}">

					<div class="form-group row">
						{{-- Nombre unidad --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.unidad_venta')) !!} *</label>
						<div class="col-lg-4">
							<x-input type="text" name="name" id="name" placeholder="{!! ucfirst(trans('textos.unidad')) !!}" autocomplete="off" maxlength="150" required value="{{ $unit->name }}"/>
						</div>	

						{{-- Tarifa --}}
						<label for="symbol" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.simbolo')) !!}</label>
						<div class="col-lg-1">
							<x-input type="text" name="symbol" id="symbol" autocomplete="off" maxlength="4" value="{{ $unit->symbol }}"/>
						</div>	

						{{-- Status --}}
						<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.activo')) !!}</label>
						<div class="col-lg-1">
							<label class="custom-chk-rdb" rel="tooltip" title="{!! trans('textos.sede_on_off') !!}">
	                            <input type="checkbox" name="status" @if($unit->status == 1) checked @endif>
	                            <span class="chk" style="top: 5px;"></span>
	                        </label>
						</div>
					</div>

					<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection
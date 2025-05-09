@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Listado de tarifas --}}
		<a href="{{ route('tariffs.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_tarifas') !!}
		</a>
	</div>
</div>
		
<div class="row mb-3">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('tariffs.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Nombre tarifa --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nombre')) !!}*</label>
						<div class="col-lg-3">
							<x-input type="text" name="name" id="name" placeholder="{!! ucfirst(trans('textos.tarifa')) !!}" autocomplete="off" maxlength="100" required/>
						</div>

						{{-- Descuento --}}
						<label for="discount" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.descuento')) !!}*</label>
						<div class="col-lg-1">
							<x-input type="text" name="discount" class="text-right set-decimals" id="discount" autocomplete="off" maxlength="8" group="right" groupvalue="%" required/>
						</div>

						{{-- Status --}}
						<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.tarifa_activa')) !!}</label>
						<div class="col-lg-1">
							<label class="custom-chk-rdb" rel="tooltip" title="{!! trans('textos.tarifa_on_off') !!}">
								<input type="checkbox" name="status" checked>
								<span class="chk" style="top: 5px;"></span>
							</label>
						</div>
					</div>

					<div class="form-group row">
						{{-- Descripción --}}
						<label for="description" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.descripcion')) !!}</label>
						<div class="col-lg-10">
							<x-input-textarea name="description"/>
						</div>
					</div>

					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>	
	</div>
</div>

@endsection
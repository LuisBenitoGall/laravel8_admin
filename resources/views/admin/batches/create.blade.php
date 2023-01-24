@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones --}}
	<div class="col-12">
		{{-- Lotes --}}
		@can('batches.index')
			<a href="{{ route('batches.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_lotes') !!}
			</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('batches.create') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Producto --}}
						<label for="product" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.producto')) !!}*</label>
						<div class="col-lg-4">
							<x-select name="product" class="select2" label="producto_selec" :options="$products" required />
						</div>

						{{-- Nombre lote --}}
						<label for="pattern" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.patron')) !!}</label>
						<div class="col-lg-4">
							<x-select name="pattern" class="select2" label="patron_selec" :options="$patterns" />
						</div>
					</div>

					<div class="form-group row">
						{{-- Fecha cierre lote --}}
						<label for="deadline" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.lote_fecha_cierre')) !!}</label>
						<div class="col-md-3">
							<x-date-picker name="deadline" id="deadline" value="" :format="$languages[config('app.locale')][4]"/>
						</div>
					</div>

					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>	
</div>

@endsection
@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		<a href="{{ route('products.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_productos') !!}
		</a>
	</div>
</div>

<div class="row mb-3">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('products.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Nombre producto --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.producto')) !!}*</label>
						<div class="col-lg-10">
							<x-input type="text" name="name" id="name" autocomplete="off" maxlength="100" required/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Tipo --}}
                        <label for="type" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.tipo')) !!} *</label>
                        <div class="col-lg-3" style="padding-top: 8px;">
							<label class="custom-chk-rdb">
				    			<input class="rdb-access" type="radio" name="type" value="p" required>
				    			<span class="rdb"></span>
				    			{!! trans('textos.producto') !!}
							</label>	

							<label class="custom-chk-rdb ml-5">
				    			<input class="rdb-access" type="radio" name="type" value="s" required>
				    			<span class="rdb"></span>
				    			{!! trans('textos.servicio') !!}
							</label>

							<x-input-errors name="type"/>
                        </div>

                        <label for="on_sale" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.producto_venta')) !!}</label>
                        <div class="col-lg-1">
							<label class="custom-chk-rdb" rel="tooltip" title="{!! trans('textos.producto_venta') !!}">
				                <input type="checkbox" name="on_sale" checked>
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
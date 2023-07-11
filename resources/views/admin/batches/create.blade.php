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
				<form method="post" autocomplete="off" action="{{ route('batches.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Producto --}}
						<label for="product" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.producto')) !!}*</label>
						<div class="col-lg-4">
							<x-input type="text" name="product_" id="search_product" placeholder="{!! ucfirst(trans('textos.producto')) !!}" maxlength="150" required/>
						</div>

						<input type="hidden" name="product_id" id="productId">
					</div>

					<div class="form-group row">
						{{-- Lote por patrón --}}
						<label for="pattern_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.patron')) !!}</label>
						<div class="col-lg-4">
							<x-select name="pattern_id" class="select2" label="patron_selec" :options="$patterns" info="batch-pattern" />
						</div>

						{{-- Número lote --}}
						<label for="batch_num" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.lote_numero')) !!}</label>
						<div class="col-lg-4">
							<x-input type="text" name="batch_num" autocomplete="off" maxlength="150" info="batch-manual"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Fecha cierre lote --}}
						<label for="deadline" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.lote_fecha_cierre')) !!}</label>
						<div class="col-md-3">
							<x-date-picker name="deadline" id="deadline" value="" :format="$languages[config('app.locale')][4]"/>
						</div>

						{{-- Fecha de caducidad --}}
						<label for="expiry_date" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.fecha_caducidad')) !!}</label>
						<div class="col-md-3">
							<x-date-picker name="expiry_date" id="expiry_date" value="" :format="$languages[config('app.locale')][4]"/>
						</div>
					</div>

					<div class="form-group row">
                        {{-- Nº documento --}}
                        <label for="doc_num" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.documento_num')) !!}</label>
                        <div class="col-lg-4">
                            <x-input type="text" name="doc_num" autocomplete="off" maxlength="100"/>
                        </div>

                        {{-- Documento --}}
                        <label for="doc" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.documento')) !!}</label>
                        <div class="col-lg-4">
                            <x-input-file name="doc"/>
                        </div>
                    </div>

					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>	
</div>

@endsection
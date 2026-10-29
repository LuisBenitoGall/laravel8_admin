<form method="post" autocomplete="off" action="{{ route('tariffs.update', $tariff) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	@method('PUT')
	{{ csrf_field() }}

	<div class="form-group row">
		{{-- Nombre tarifa --}}
		<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nombre')) !!}*</label>
		<div class="col-lg-3">
			<x-input type="text" name="name" id="name" placeholder="{!! ucfirst(trans('textos.tarifa')) !!}" autocomplete="off" maxlength="100" required value="{{ $tariff->name }}"/>
		</div>

		{{-- Descuento --}}
		<label for="discount" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.descuento')) !!}*</label>
		<div class="col-lg-1">
			<x-input type="text" name="discount" class="text-right set-decimals" id="discount" autocomplete="off" maxlength="8" group="right" groupvalue="%" required value="{{ $tariff->discount }}"/>
		</div>

		{{-- Status --}}
		<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.tarifa_activa')) !!}</label>
		<div class="col-lg-1">
			<label class="custom-chk-rdb" rel="tooltip" title="{!! trans('textos.tarifa_on_off') !!}">
				<input type="checkbox" name="status" @if($tariff->status == 1) checked @endif>
				<span class="chk" style="top: 5px;"></span>
			</label>
		</div>
	</div>

	<div class="form-group row">
		{{-- Descripción --}}
		<label for="description" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.descripcion')) !!}</label>
		<div class="col-lg-10">
			<x-input-textarea name="description" value="{{ $tariff->description }}"/>
		</div>
	</div>

	<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
</form>
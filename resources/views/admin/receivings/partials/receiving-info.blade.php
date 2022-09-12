<form method="post" autocomplete="off" action="{{ route('receiving.update', $delivery) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	@method('PUT')
	{{ csrf_field() }}

	<div class="form-group row">
		{{-- Fecha recepción --}}
		<label for="reception_date" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.recepcion_fecha')) !!}</label>
		<div class="col-lg-2">
			@php
			$receptionDate = $delivery->reception_date != ''? \Carbon\Carbon::parse($delivery->reception_date)->format($languages[session()->get('locale')][4]):'';
			@endphp

			<x-date-picker name="reception_date" value="{{ $receptionDate }}" :format="$languages[config('app.locale')][4]"/>
		</div>

		{{-- Cerrar recepción --}}
		<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.recepcion_cerrar')) !!}</label>
		<div class="col-lg-1">
			<label class="custom-chk-rdb" rel="tooltip">
                <input type="checkbox" name="status">
                <span class="chk" style="top: 5px;"></span>
            </label>

            <div class="position-absolute" style="left: 65px; top: 0;">
            	{!! App\Helpers\helpers::info('receiving-close') !!}
            </div>
		</div>
	</div>

	{{-- Observaciones --}}
	<div class="form-group row">
		<label for="customer_observations" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.observaciones')) !!}</label>
		<div class="col-lg-10">
			<x-input-textarea name="customer_observations" class="editor" value="{{ $delivery->customer_observations }}"/>
		</div>
	</div>

	<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
</form>
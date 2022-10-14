<form method="post" autocomplete="off" action="{{ route('invoices.update', $invoice) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	@method('PUT')
	{{ csrf_field() }}

	<div class="form-group row">
		{{-- Nº de factura --}}
		<label for="pattern_id" class="col-md-3 col-lg-2 col-form-label">{!! ucfirst(trans('textos.factura_num')) !!}</label>
		<div class="col-md-3 col-lg-2">
			{{-- Patrón de numeración - condiciones:
			 	1. Si existen patrones.
				3. Exclusivo para proveedor.
				...
			--}}

			@if($patterns && $order->provider_id == session('currentCompany'))
				@if($patterns->count())
					@php
					$patterns_ = [];
					foreach($patterns as $ptn){
						$patterns_[$ptn->id] = $ptn->name;
					}
					@endphp

					<x-select name="pattern_id" class="select2" label="patron_selecciona" :options="$patterns_" info="invoice-pattern-num"/>
				@else
					<label class="text-warning mt-2">{{ trans('textos.patrones_no_hay') }}</label>
				@endif

			@else
				<label class="text-warning mt-1" style="font-size: 16px;"><b><u>
					@if($invoice->invoice_num)
						{{ $invoice->invoice_num }}
					@else
						{{ $invoice->id }}
					@endif
				</u></b></label>
			@endif

		</div>

		{{-- Factura nº manual --}}
		<label for="invoice_old_num" class="col-md-3 col-lg-2 col-form-label">{!! ucfirst(trans('textos.factura_antigua')) !!}</label>
		<div class="col-md-3 col-lg-2">
			<x-input type="text" name="invoice_old_num" value="{{ $invoice->invoice_old_num }}" autocomplete="off" maxlength="150" info="invoice-old-num" readonly="{{ $readonly }}"/>
		</div>
	</div>

	<div class="form-group row">
		{{-- Fecha fiscal --}}
		<label for="fiscal_date" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.fecha_fiscal')) !!}</label>
		<div class="col-md-4 col-lg-2">
			@php
			$fiscal_date = $invoice->fiscal_date != ''? \Carbon\Carbon::parse($invoice->fiscal_date)->format($languages[session()->get('locale')][4]):'';
			@endphp	

			<x-date-picker name="fiscal_date" value="{{ $fiscal_date }}" :format="$languages[config('app.locale')][4]" disabled="{{ $disabled }}"/>
		</div>

		{{-- Fecha vencimiento --}}
		<label for="expiration_date" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.fecha_vencimiento')) !!}</label>
		<div class="col-md-4 col-lg-2">
			@php
			$expiration_date = $invoice->expiration_date != ''? \Carbon\Carbon::parse($invoice->expiration_date)->format($languages[session()->get('locale')][4]):'';
			@endphp	

			<x-date-picker name="expiration_date" value="{{ $expiration_date }}" :format="$languages[config('app.locale')][4]" disabled="{{ $disabled }}"/>
		</div>

		{{-- Estado --}}
		<label for="status" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.estado')) !!}</label>
		<div class="col-md-4 col-lg-2">
			@php
			$estados = App\Helpers\helpers::invoiceStatus();
			@endphp

			<x-select name="status" class="select2" label="indica_estado" :options="$estados" value="{{ $invoice->status }}" disabled="{{ $disabled }}"/>	
		</div>
	</div>

	<div class="form-group row">
		{{-- Observaciones --}}
		<label for="observations" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.observaciones')) !!}</label>
		<div class="col-md-10">
			@php
			//Se editan las observaciones del cliente o del proveedor según sea vista de compra o venta.
			$observations = $is_sale? $invoice->provider_observations:$invoice->customer_observations;
			@endphp

			<x-input-textarea name="observations" class="editor" value="{{ $observations }}"/>
		</div>
	</div>

	<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
</form>
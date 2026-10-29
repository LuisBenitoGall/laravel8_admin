<div class="row">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('effects.groups-filter') }}" accept-charset="utf-8"
				enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						<div class="col-md-10 offset-md-2">
							<p class="text-warning">{!! trans('textos.efectos_agrupados_texto') !!}</p>
						</div>
					</div>

					<div class="form-group row">
						{{-- Clientes --}}
						<label for="customer" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.cliente')) !!}</label>
			            <div class="col-md-4 position-relative">
			                <x-input type="text" name="customer" id="search_customer" placeholder="{!! ucfirst(trans('textos.cliente')) !!}" maxlength="150"/>
			            </div>

			            <input type="hidden" name="customer_id" id="customerId">

			            {{-- Proveedores --}}
			            <label for="provider" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.proveedor')) !!}</label>
			            <div class="col-md-4 position-relative">
			                <x-input type="text" name="provider" id="search_provider" placeholder="{!! ucfirst(trans('textos.proveedor')) !!}" maxlength="150"/>
			            </div>

			            <input type="hidden" name="provider_id" id="providerId">
			        </div>

					<div class="form-group row">
						{{-- Desde --}}
						<label for="from" class="col-md-2 col-lg-2 col-form-label">{!! ucfirst(trans('textos.desde')) !!}</label>
						<div class="col-md-4 col-lg-2">
							@php
							$from = $from && $from != 'x'? \Carbon\Carbon::parse($from)->format($languages[session()->get('locale')][4]):'';
							@endphp

							<x-date-picker name="from" id="from" value="{{ $from }}" :format="$languages[config('app.locale')][4]"/>
						</div>

						{{-- Hasta --}}
						<label for="to" class="col-md-2 col-lg-2 col-form-label">{!! ucfirst(trans('textos.hasta')) !!}</label>
						<div class="col-md-4 col-lg-2">
							@php
							$to = $to && $to != 'x'? \Carbon\Carbon::parse($to)->format($languages[session()->get('locale')][4]):'';
							@endphp

							<x-date-picker name="to" id="from" value="{{ $to }}" :format="$languages[config('app.locale')][4]"/>
						</div>

						{{-- Métodos de pago --}}
						<label for="payment_method" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.pago_metodo')) !!}</label>
						<div class="col-lg-2">
							<x-select name="payment_method" class="select2" :options="$payment_methods" label="metodo_selec" value="{{ $payment_method }}"/>
						</div>
					</div>

					<x-btn-submit name="submit_filter_groups" label="{{ trans('textos.buscar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

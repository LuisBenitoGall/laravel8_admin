@php
$discounts = $conditions->discounts? unserialize($conditions->discounts):false;
@endphp

{{-- Datos comerciales de cliente --}}
@if($business_relationship == 'customers')
	<form method="post" autocomplete="off" action="{{ route('customers.commercial-data') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
		{{ csrf_field() }}
		<input type="hidden" name="customer" value="{{ $company->id }}">	

		<div class="form-group row">
			<label for="tariff" class="col-lg-2 col-form-label">{{ ucfirst(trans('textos.tarifa')) }}</label>
			<div class="col-lg-4">
				@php
				$tc = $tariff_customer? $tariff_customer->id:'';
				@endphp
				<x-select name="tariff" class="select2" label="centro_trabajo_selec" :options="$tariffs" value="{{ $tc }}"/>
			</div>
		</div>

		<x-btn-submit name="submit_commercial_data" label="{{ trans('textos.guardar') }}"/>
	</form>
@endif

<div class="row mt-4">
	<div class="table-responsive">
 		<table class="table table-hover dtabla" id="tblCustomerProvidersCommercial">
			<thead>
				<tr>
					<th class="text-center wdt50">#</th>
					<th>{{ ucfirst(trans('textos.concepto')) }}</th>
					<th class="text-center">{!! ucfirst(trans('textos.descuento')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>

			<tbody>
				@if($discounts && count($discounts))
					@foreach($discounts as $idx => $dto)
						<tr>
							{{-- Orden --}}
							<td class="text-center wdt50">{{ $idx }}</td>
							{{-- Concepto --}}
							<td>{{ $dto['concept'] }}</td>
							{{-- Descuento --}}
							<td class="text-right">{{ $dto['discount'] }}%</td>
							{{-- Acciones --}}
							<td class="text-right">
								{{-- Eliminar --}}
								<form method="POST" action="{{ route('customers.delete-discount') }}" class="frm-inline" role="form" accept-charset="UTF-8">
								    <input name="_method" type="hidden" value="DELETE">
								    <input type="hidden" name="index" value="{{ $idx }}">
								    <input type="hidden" name="cp_id" value="{{ $conditions->id }}">
								    {{ csrf_field() }}
								    <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.descuento_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
								</form>
							</td>
						</tr>
					@endforeach
				@endif
			</tbody>
		</table>
	</div>
</div>
@push('scripts')
<script>
$(function(){
	//Tabla condiciones comerciales:
	var tableCustomerProvidersCommercial = $('#tblCustomerProvidersCommercial').DataTable({
        serverSide: false,
        processing: false
    });
});
</script>
@endpush
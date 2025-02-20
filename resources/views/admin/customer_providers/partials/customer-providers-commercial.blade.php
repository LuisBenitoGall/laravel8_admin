@php
$discounts = $conditions->discounts? unserialize($conditions->discounts):false;
@endphp

<div class="row">
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
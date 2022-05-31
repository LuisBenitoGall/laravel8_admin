@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('currencies.create')
			<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-new-currency"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.moneda_nueva')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblCurrencies">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.moneda')) !!}</th>
					<th>{!! ucfirst(trans('textos.codigo')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.numero')) !!} (ISO 4217)</th>
					<th>{!! ucfirst(trans('textos.simbolo')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

{{-- Modals --}}
{{-- Nueva moneda: --}}
@include('admin.currencies.partials.modal-add-currency')

{{-- Editar moneda: --}}
@include('admin.currencies.partials.modal-edit-currency')

@push('scripts')
<script>
{{-- Modal editar moneda --}}
$(document).on('click', '.btnEditCurrency', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    var currency = $(this).data('currency');
    var symbol = $(this).data('symbol');
    var action = '/admin/currencies/'+id;

    $('#frm-edit-currency').attr('action', action);
    $('#currencyName').val(currency);
    $('#currencyId').val(id);
    $('#currencySymbol').val(symbol);
});
</script>
@endpush

@endsection
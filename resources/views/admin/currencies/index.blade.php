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
<div class="modal fade opened-modal-save" id="modal-new-currency" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{!! ucfirst(trans('textos.moneda_nueva')) !!}</h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
				<form method="post" action="{{ route('currencies.store') }}" autocomplete="off" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						<div class="col-lg-9">
							<div class="row">
								{{-- Nombre moneda --}}
								<label for="name" class="col-lg-12">{!! ucfirst(trans('textos.moneda')) !!} *</label>
								<div class="col-lg-12">
									<x-input type="text" name="name" maxlength="191" required info="currency-name"/>
								</div>
							</div>
						</div>

						<div class="col-lg-3">
							<div class="row">
								{{-- Status moneda --}}
								<label for="status" class="col-lg-12">{!! ucfirst(trans('textos.moneda_activa')) !!}</label>
								<div class="col-lg-12 text-center">
									<x-checkbox name="status" checked="true" style="top: 5px;"/>
								</div>
							</div>
						</div>
					</div>

					<div class="form-group row">
						<div class="col-lg-4">
							<div class="row">
								{{-- Código --}}
								<label for="code" class="col-lg-12">{!! ucfirst(trans('textos.codigo')) !!}</label>	
								<div class="col-lg-12">
									<x-input type="text" name="code" maxlength="3" info="currency-code"/>
								</div>
							</div>
						</div>

						<div class="col-lg-4">
							<div class="row">
								{{-- Número ISO --}}
								<label for="number" class="col-lg-12">{!! ucfirst(trans('textos.numero')) !!}</label>	
								<div class="col-lg-12">
									<x-input type="text" name="number" maxlength="4" info="currency-number"/>
								</div>
							</div>
						</div>

						<div class="col-lg-4">
							<div class="row">
								{{-- Símbolo --}}
								<label for="symbol" class="col-lg-12">{!! ucfirst(trans('textos.simbolo')) !!}</label>	
								<div class="col-lg-12">
									<x-input type="text" name="symbol" maxlength="2" info="currency-symbol"/>
								</div>
							</div>
						</div>
					</div>
					
					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
        </div>
    </div>
</div>

{{-- Editar moneda: --}}
<div class="modal fade opened-modal-edit" id="modal-edit-currency" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{!! ucfirst(trans('textos.moneda_editar')) !!}</h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
				<form method="post" action="" id="frm-edit-currency" autocomplete="off" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}
					<input type="hidden" name="id" id="currencyId">

					<div class="form-group row">
						{{-- Nombre moneda --}}
						<label for="name" class="col-lg-12" required>{!! ucfirst(trans('textos.moneda')) !!} *</label>
						<div class="col-lg-12">
							<x-input type="text" name="name" id="currencyName" maxlength="191" required info="currency-name"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Símbolo --}}
						<label for="symbol" class="col-lg-12">{!! ucfirst(trans('textos.simbolo')) !!}</label>	
						<div class="col-lg-4">
							<x-input type="text" name="symbol" id="currencySymbol" maxlength="2" info="currency-symbol"/>
						</div>
					</div>
					
					<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
        </div>
    </div>
</div>

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
@extends('admin.partials.layout')

@section('content')

@php
//Los efectos sólo pueden modificarse si su estado es de pendiente:
$disabled = $effect->status == config('constants.EFFECT_STATUS_PENDENT_')? false:true;
@endphp

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-sm-12">
		{{-- Volver a efectos --}}
		<a href="{{ route('effects.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_efectos') !!}
		</a>

		{{-- Nuevo efecto --}}
		@can('effects.create')
			<a href="{{ route('effects.create') }}" class="btn btn-primary">
				<i class="fa fa-plus"></i>
				{{ trans('textos.efecto_nuevo') }}
			</a>
		@endcan

		{{-- Agregar facturas - condiciones
		1. Permisos de edición de efectos.
		2. Efecto en estado pendiente.
		...
		--}}
		{{-- @can('effects.edit')
			{{-- @if($effect->status == config('constants.EFFECT_STATUS_PENDENT_')) --}}
			{{-- <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#modal-add-invoices">
				<i class="fa fa-plus"></i>
				{{ trans('textos.facturas_agregar') }}
			</a> --}}
			{{-- @endif --}}
		{{-- @endcan --}}
	</div>
</div>

<div class="row mb-3">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				{{-- Info --}}
				<div class="mb-4">
					{{-- Fecha alta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.fecha_alta')) !!}:
						<b>
						{{ $effect->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Tipo de agrupación --}}
					<label class="mr-5">
						@if($effect->is_group)
							<b>{{ trans('textos.efectos_agrupacion') }}</b>
						@else
							<b>{{ trans('textos.efecto_simple') }}</b>
							@if($effect->parent)
								, {{ trans('textos.efecto_agrupado_en') }} <a href="{{ route('effects.edit', $effect->parent) }}">{{ $effect->parent }}</a>
							@endif
						@endif
					</label>

					{{-- Remesa --}}
					@if($remittance)
						<label class="mr-5">
							{!! ucfirst(trans('textos.remesa')) !!}:	
							<b>
								@php
								$remittanceNum = $remittance->remittance_num? $remittance->remittance_num:($remittance->remittance_old_num? $remittance->remittance_old_num:$remittance->id);
								@endphp
								<a href="{{ route('remittances.edit', $remittance->id) }}">{{ $remittanceNum }}</a>
							</b>
						</label>
					@endif

					{{-- Autor --}}
					<label class="mr-5">
					{!! ucfirst(trans('textos.creado_por')) !!}:
					<b>
					{{ $created_by->name }} {{ $created_by->surname }}
					</b>
					</label>

					{{-- Fecha última actualización --}}
					<label class="mr-5">
					{!! ucfirst(trans('textos.ultima_edicion')) !!}:
					<b>
					{{ $effect->updated_at->format($languages[session()->get('locale')][4].' H:i:s') }}
					</b>
					</label>
				</div>

				<form method="post" autocomplete="off" action="{{ route('effects.update', $effect) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}
					<input type="hidden" name="update" value="{{ $disabled }}">

					<div class="form-group row">
						{{-- Clientes --}}
						<label for="customer" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cliente')) !!}</label>
						<div class="col-lg-4">
							<x-input type="text" name="customer" autocomplete="off" maxlength="150" value="{{ $customer->name }}" disabled/>
						</div>

						{{-- Importe --}}
						<label for="amount" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.importe')) !!}</label>
						<div class="col-lg-2">
							<x-input type="text" name="amount" class="text-right set-decimals" autocomplete="off" maxlength="15" group="right" groupvalue="{{ $currency->symbol }}" value="{{ $effect->amount }}" disabled="{{ $disabled }}"/>
						</div>
					</div>

					@php
					//La cuenta propia es la de proveedor o cliente según sea la empresa en session cliente o proveedor en el efecto.
					if($effect->company_id == $effect->provider_id){
						$my_bank_account_ = $effect->provider_bank_account_id;
						$bank_account_ = $effect->customer_bank_account_id;	
					}else{
						$my_bank_account_ = $effect->customer_bank_account_id;
						$bank_account_ = $effect->provider_bank_account_id;	
					}
					@endphp

					<div class="form-group row">
						{{-- Cuenta bancaria propia --}}
						<label for="bank_account" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cuenta_bancaria_propia')) !!}*</label>
						<div class="col-lg-4">
							<x-select name="my_bank_account" class="select2" :options="$my_bank_accounts" value="{{ $my_bank_account_ }}" label="cuenta_selec" required disabled="{{ $disabled }}"/>
						</div>

						{{-- Cuenta bancaria --}}
						<label for="bank_account" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cuenta_bancaria_ajena')) !!}*</label>
						<div class="col-lg-4">
							<x-select name="bank_account" class="select2" :options="$bank_accounts" label="cuenta_selec" value="{{ $bank_account_ }}" required disabled="{{ $disabled }}"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Fecha vencimiento --}}
						<label for="date" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.fecha_vencimiento')) !!}*</label>
						<div class="col-lg-2">
							@php
							$expiration_date = $effect->date != ''? \Carbon\Carbon::parse($effect->date)->format($languages[session()->get('locale')][4]):'';
							@endphp
							<x-date-picker name="date" :format="$languages[config('app.locale')][4]" required value="{{ $expiration_date }}" disabled="{{ $disabled }}"/>
						</div>

						{{-- Métodos de pago --}}
						<label for="payment_method" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.pago_metodo')) !!}*</label>
						<div class="col-lg-2">
							<x-select name="payment_method" class="select2" :options="$payment_methods" label="metodo_selec" value="{{ $effect->payment_method_id }}" required disabled="{{ $disabled }}"/>
						</div>

						{{-- Estado del pago --}}
						<label for="payment_status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.pago_estado')) !!}</label>
						<div class="col-lg-2">
							<x-select name="payment_status" class="select2" :options="$payment_status" value="{{ $effect->payment_status }}" id="effectPaymentStatus" label="estado_selec"/>
						</div>

						{{-- Estado --}}
						{{-- <label for="payment_status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.estado')) !!}*</label>
						<div class="col-lg-2">
							<x-select name="payment_status" class="select2" :options="$effect_status" label="estado_selec" value="{{ $effect->status }}" required/>
						</div> --}}
					</div>

					<div class="form-group row">
						{{-- Observaciones --}}
						<label for="observations" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.observaciones')) !!}</label>
						<div class="col-lg-10">
							<x-input-textarea name="observations" value="{{ $effect->observations }}"/>
						</div>
					</div>

					<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/> 
				</form>
			</div>
		</div>
	</div>
</div>

<div class="row mt-4">
	<div class="table-responsive">
		@if($effect->is_group && $son_effects)
			<div class="col-12">
				<h3>Efectos vinculados</h3>	
			</div>

			{{-- <table class="table table-hover dTable" id="tblEffectInvoices"> --}}
			<table class="table table-hover dTable" id="tblGroupEffects">
				<thead>
					<tr>
						<th class="text-center wdt50">#</th>
						<th class="text-center wdt150">{!! ucfirst(trans('textos.cliente')) !!}</th>
						{{-- <th class="text-center wdt50">{!! ucfirst(trans('textos.fecha')) !!}</th> --}}
						<th class="text-center wdt100">{!! ucfirst(trans('textos.fecha_vencimiento')) !!}</th>
						<th class="text-center wdt100">{!! trans('textos.importe') !!}</th>
						<th class="wdt100">{!! trans('textos.estado') !!}</th>
						<th class="text-center wdt100">{!! ucfirst(trans('textos.acciones')) !!}</th>
					</tr>
				</thead>

				@php
				$total_amount = 0;
				@endphp

				<tbody>
					@foreach($son_effects as $ef)
						<tr>
							{{-- Nº efecto --}}
							<td class="text-right">
								@php
								$effectNum = $ef->ef_num? $ef->e_num:$ef->id;
								@endphp
								<a target="_blank" href="{{ route('effects.edit', $ef->id) }}">{{ $effectNum }}</a>
							</td>
							{{-- Empresa --}}
							<td>
								@php
								$company = $ef->provider_id == $ef->company_id? $ef->customer:$ef->provider;
								@endphp
								{{ $company }}
							</td>
							{{-- Fecha vencimiento --}}
							<td class="text-right">
								@if($ef->date)
								{!! \Carbon\Carbon::parse($ef->date)->format($languages[session()->get('locale')][4]); !!} 
								@endif 
							</td>
							{{-- Importe --}}
							<td class="text-right">
								{{ $ef->amount }}{{ $currency->symbol }}
							</td>
							{{-- Estado --}}
							<td>
								@if(in_array($ef->status, $effect_status)) {!! $effect_status[$ef->status] !!} @endif
							</td>
							{{-- Acciones --}}
							<td class="text-right">
								@can('effects.edit')
									{{-- Editar --}}
									<a target="_blank" class="btn btn-info btn-xs" href="{{ route('effects.edit', $ef->id) }}" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>

									{{-- Desvincular - condiciones: 
										1. El efecto padre debe estar aún pendiente.
										...
									--}}
									@if($effect->status == config('constants.EFFECT_STATUS_PENDENT_'))
										<form method="POST" action="{{ route('effects.unlink', $ef->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
											<input name="_method" type="hidden" value="DELETE">
											{{ csrf_field() }}
											<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.desvincular')) }}" data-msg="{{ ucfirst(trans('textos.efecto_confirm_desvincular')) }}"><i class="fa fa-trash"></i></button>
										</form>
									@endif
								@endcan
							</td>
						</tr>
					@endforeach








					{{-- @foreach($invoices as $inv)
						@php
						$total_amount = $total_amount + $inv->amount_with_iva;
						@endphp

						<tr>
							{{-- Nº factura --}}
							{{-- <td class="text-right">
								@php
								$invoiceNum = $inv->invoice_num? $inv->invoice_num:($inv->invoice_old_num? $inv->invoice_old_num:$inv->id);
								@endphp
								<a target="_blank" href="{{ route('invoices.show', $inv->id) }}">{{ $invoiceNum }}</a>
							</td> --}}
							{{-- Empresa --}}
							{{-- <td>
								{{ $inv->company }}
							</td> --}}
							{{-- Fecha --}}
							{{-- <td class="text-right">
								@if($inv->fiscal_date)
								{!! \Carbon\Carbon::parse($inv->fiscal_date)->format($languages[session()->get('locale')][4]); !!} 
								@endif
							</td> --}}
							{{-- Fecha vencimiento --}}
							{{-- <td class="text-right">
								@if($inv->expiration_date)
								{!! \Carbon\Carbon::parse($inv->expiration_date)->format($languages[session()->get('locale')][4]); !!} 
								@endif 
							</td> --}}
							{{-- Importe --}}
							{{-- <td class="text-right">
								{{ $inv->amount_with_iva }}{{ $currency->symbol }}
							</td> --}}
							{{-- Estado --}}
							{{-- <td>
								@if(in_array($inv->status, $invoice_status)) {!! $invoice_status[$inv->status] !!} @endif
							</td> --}}
							{{-- Acciones --}}
							{{-- <td class="text-right"> --}}
								{{-- Editar --}}
								{{-- <a target="_blank" class="btn btn-info btn-xs" href="{{ route('invoices.show', $inv->id) }}" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>
							</td>
						</tr>
					@endforeach --}}
				</tbody>

				{{-- <tfoot>
					<tr>
						<td colspan="4" class="text-right">{{ trans('textos.total') }}</td>
						<td class="text-right">
							{{ $total_amount }}{{ $currency->symbol }} 
						</td>
						<td></td>
						<td></td>
					</tr>
				</tfoot> --}}
			</table>
		@endif
	</div>
</div>

{{-- Modals --}}
{{-- Add invoices --}}
@include('admin.effects.partials.modal-add-invoices')

@endsection

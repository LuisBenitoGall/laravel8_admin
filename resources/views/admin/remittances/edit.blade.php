@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-sm-12">
		<a href="{{ route('remittances.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_remesas') !!}
		</a>

		@can('remittances.create')
			{{-- Nueva remesa de cobros --}}
			<a class="btn btn-info" href="{{ route('remittances.create', 'collection') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.remesa_nueva_cobros')) !!}</a>

			{{-- Nueva remesa de pagos --}}
			<a class="btn btn-info" href="{{ route('remittances.create', 'payment') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.remesa_nueva_pagos')) !!}</a>
		@endcan

		{{-- Generar XML --}}
		<a href="{{ route('remittances.generate-xml', $remittance) }}" class="btn btn-info btn-confirm" data-msg="{{ ucfirst(trans('textos.xml_generar_confirm')) }}">
			<i class="fa fa-file-alt"></i>
			{!! trans('textos.xml_generar') !!}
		</a>

		{{-- Descargar XML --}}
		@if($remittance->doc)
			<a target="_blank" href="{{ asset('storage/remittances/'.$remittance->doc) }}" class="btn btn-info">
				<i class="fa fa-file-download"></i>
				{!! trans('textos.xml_descargar') !!}
			</a>	
		@endif
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-3">
					{!! ucfirst(trans('textos.remesa')) !!} 
					<u>
						@if($remittance->remittance_num)	
							{{ $remittance->remittance_num }}
						@elseif($remittance->remittance_old_num)	
							{{ $remittance->remittance_old_num }}
						@else
							{{ $remittance->id }}
						@endif
					</u>
				</h3>	

				{{-- Info --}}
				<div class="mb-4">
					{{-- Tipo --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.tipo')) !!}:
						<b>
							@if($remittance->type == 1) 
								{!! ucfirst(trans('textos.cobro')) !!} 
							@else
								{!! ucfirst(trans('textos.pago')) !!}
							@endif
						</b>
					</label>

					{{-- Autor --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.autor')) !!}: <b>{{ $author->name }} {{ $author->surname }}</b>
					</label>

					{{-- Fecha alta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.fecha_alta')) !!}: 
						<b>
							{{ $remittance->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Fecha última actualización --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.ultima_edicion')) !!}: 
						<b>
							{{ $remittance->updated_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>
				</div>

				<form method="post" autocomplete="off" action="{{ route('remittances.update', $remittance->id) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Nº de remesa --}}
						<label for="pattern_id" class="col-md-3 col-lg-2 col-form-label">{!! ucfirst(trans('textos.remesa_num')) !!}</label>
						<div class="col-md-3">
							{{-- Patrón de numeración - condiciones:
							 	1. Si existen patrones.
								3. Exclusivo para proveedor.
								4. No existe remittance_num.
								...
							--}}
							@if($patterns && $remittance->company_id == session('currentCompany') && !$remittance->remittance_num)
								@if(count($patterns))
									<x-select name="pattern_id" class="select2" label="patron_selecciona" :options="$patterns" info="remittance-pattern-num"/>
								@else
									<label class="text-warning mt-2">{{ trans('textos.patrones_no_hay') }}</label>
								@endif

							@else
								<label class="text-warning mt-1" style="font-size: 16px;"><b><u>
									@if($remittance->order_num)
										{{ $remittance->order_num }}
									@else
										{{ $remittance->id }}
									@endif
								</u></b></label>
							@endif

							{{-- Reset de número - condiciones:
								3. Exclusivo para proveedor.
							--}}
							@if($remittance->remittance_num && $remittance->provider_id == session('currentCompany'))
								<a href="{{ route('orders.reset-num', $order->id) }}" class="ml-3 btn-confirm" rel="tooltip" title="{{ trans('textos.numero_reset') }}" data-msg="{{ trans('textos.numero_reset_confirm') }}">
									<i class="fa fa-redo"></i>
								</a>
							@endif
						</div>

						{{-- Remesa nº manual --}}
						<label for="remittance_old_num" class="col-md-3 col-lg-2 col-form-label">{!! ucfirst(trans('textos.remesa_num_manual')) !!}</label>
						<div class="col-md-3">
							<x-input type="text" name="remittance_old_num" value="{{ $remittance->remittance_old_num }}" autocomplete="off" maxlength="150" info="remittance-old-num"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Cuenta bancaria --}}
						<label for="bank_account" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cuenta_bancaria')) !!}*</label>
						<div class="col-lg-2">
							<x-select name="bank_account" class="select2" :options="$bank_accounts" label="cuenta_selec" value="{{ $remittance->bank_account_id }}" required/>
						</div>

						{{-- Precio --}}
						<label for="amount" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.importe')) !!}</label>
						<div class="col-lg-2">
							<x-input type="text" name="amount" class="text-right set-decimals" id="price" value="{{ $remittance->amount }}" autocomplete="off" maxlength="8" group="right" groupvalue="{{ $currency->symbol }}" readonly/>
						</div>

						{{-- Estado --}}
						<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.estado')) !!}</label>
						<div class="col-lg-2">
							<x-select name="status" class="select2" label="estado_selec" :options="$estados" value="{{ $remittance->status }}"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Fecha envío --}}
						<label for="send_date" class="col-md-2 col-lg-2 col-form-label">{!! ucfirst(trans('textos.fecha_envio')) !!}</label>
						<div class="col-md-4 col-lg-2">
							@php
							$sendDate = $remittance->send_date != ''? \Carbon\Carbon::parse($remittance->send_date)->format($languages[session()->get('locale')][4]):'';
							@endphp
							
							<x-date-picker name="send_date" value="{{ $sendDate }}" :format="$languages[config('app.locale')][4]"/>
						</div>

						{{-- Fecha pago --}}
						<label for="paying_date" class="col-md-2 col-lg-2 col-form-label">{!! ucfirst(trans('textos.pago_fecha')) !!}</label>
						<div class="col-md-4 col-lg-2">
							@php
							$payingDate = $remittance->paying_date != ''? \Carbon\Carbon::parse($remittance->paying_date)->format($languages[session()->get('locale')][4]):'';
							@endphp
							
							<x-date-picker name="paying_date" value="{{ $payingDate }}" :format="$languages[config('app.locale')][4]"/>
						</div>
					</div>

					<div id="effects">
						{{-- Se almacenan los effects que se vayan seleccionando --}}
						@foreach($selected_effects as $sf_)
							<input type="hidden" class="ef-{{ $sf_->id }}" name="effects[]" value="{{ $sf_->id }}">
						@endforeach
					</div>

					<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/> 				
				</form>
			</div>
		</div>
	</div>
</div>

<div class="row mb-3">
	<div class="table-responsive">
		<table class="table table-hover dTable" id="tblRemittanceEffects">
			<thead>
				<th class="text-center wdt50">{!! ucfirst(trans('textos.efectos')) !!}</th>
				<th class="wdt100">
					@if($remittance->type == 1) 
						{!! ucfirst(trans('textos.cliente')) !!} 
					@else
						{!! ucfirst(trans('textos.proveedor')) !!}
					@endif
				</th>
				<th class="wdt50">{!! ucfirst(trans('textos.factura')) !!}</th>
				<th class="text-center wdt50">{!! ucfirst(trans('textos.fecha')) !!}</th>
				<th class="text-center wdt50">{!! ucfirst(trans('textos.importe')) !!}</th>
				<th class="text-center wdt50">{!! ucfirst(trans('textos.tipo')) !!}</th>
				<th class="text-center wdt150">{!! ucfirst(trans('textos.cuenta_bancaria')) !!}</th>
				<th class="text-center wdt50">{!! ucfirst(trans('textos.acciones')) !!}</th>
			</thead>

			<tbody>
				{{-- Efectos remesados --}}
				@foreach($selected_effects as $sf)
					<tr>
						{{-- Número --}}
						<td class="text-right">
							@if($sf->effect_num) 
								<a target="_blank" href="{{ route('effects.edit', $sf->id) }}">{{ $sf->effect_num }}</a>
							@endif
						</td>
						{{-- Cliente --}}
						<td>
							{{ $sf->company }}
						</td>
						{{-- Factura --}}
						<td class="text-right">
							@php
							$invoices = $sf->invoices;
							$k = 0;
							@endphp

							@foreach($invoices as $inv)
								@if($k > 0)
									,
								@endif

								@php
								$num = $inv->invoice_num? $inv->invoice_num:($inv->invoice_old_num? $inv->invoice_old_num:$inv->invoice_id);
								@endphp

								<a target="_blank" href="{{ route('invoices.edit', $inv->id) }}">{{ $num }}</a>

								@php
								$k++;
								@endphp
							@endforeach
						</td>
						{{-- Fecha --}}
						<td class="text-right">
							{!! Carbon\Carbon::parse($sf->date)->format($languages[session()->get('locale')][4]); !!}
						</td>
						{{-- Importe --}}
						<td class="text-right">
							{{ $sf->amount }}{{ $sf->symbol }}
						</td>
						{{-- Tipo de efecto --}}
						<td>
							@if($sf->type && isset($effect_types[$sf->type])) {!! $effect_types[$sf->type] !!} @endif
						</td>
						{{-- Cuenta bancaria --}}
						<td class="text-right">
							{{ $sf->bank }}, {{ $sf->account }}	
						</td>
						{{-- Acciones --}}
						<td class="text-right">
							{{-- Vincular a remesa - condiciones: 
								1. Si la remesa está en estado pendiente.
								... 
							--}}
							@if($remittance->status == config('constants.EFFECT_STATUS_PENDENT_'))
								<label class="custom-chk-rdb" rel="tooltip" title="{{ trans('textos.efecto_on_off') }}">
									<input type="checkbox" name="effect" value="{{ $sf->id }}" class="chkEffect" checked><span class="chk"></span>
								</label>
							@endif

							{{-- Editar --}}
							<a target="_blank" class="btn btn-xs btn-info" href="{{ route('effects.edit', $sf->id) }}" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}">
								<i class="fa fa-edit"></i>
							</a>
						</td>
					</tr>
				@endforeach

				{{-- Efectos remesables - condiciones: 
					1. Si la remesa está en estado Abierto.
					...
				--}}
				@if($remittance->status == config('constants.REMITTANCE_STATUS_OPEN_'))
					@foreach($effects as $ef)
						<tr>
							{{-- Número --}}
							<td class="text-right">
								@if($ef->effect_num) 
									<a target="_blank" href="{{ route('effects.edit', $ef->id) }}">{{ $ef->effect_num }}</a>
								@endif
							</td>
							{{-- Cliente --}}
							<td>
								{{ $ef->company }}
							</td>
							{{-- Factura --}}
							<td class="text-right">
								@php
								$invoices = $ef->invoices;
								$k = 0;
								@endphp

								@foreach($invoices as $inv)
									@if($k > 0)
										,
									@endif

									@php
									$num = $inv->invoice_num? $inv->invoice_num:($inv->invoice_old_num? $inv->invoice_old_num:$inv->invoice_id);
									@endphp

									<a target="_blank" href="{{ route('invoices.edit', $inv->id) }}">{{ $num }}</a>

									@php
									$k++;
									@endphp
								@endforeach
							</td>
							{{-- Fecha --}}
							<td class="text-right">
								{!! Carbon\Carbon::parse($ef->date)->format($languages[session()->get('locale')][4]); !!}
							</td>
							{{-- Importe --}}
							<td class="text-right">
								{{ $ef->amount }}{{ $ef->symbol }}
							</td>
							{{-- Tipo de efecto --}}
							<td>
								@if($ef->type && isset($effect_types[$ef->type])) {!! $effect_types[$ef->type] !!} @endif
							</td>
							{{-- Cuenta bancaria --}}
							<td class="text-right">
								{{ $ef->bank }}, {{ $ef->account }}	
							</td>
							{{-- Acciones --}}
							<td class="text-right">
								{{-- Vincular a remesa --}}
								<label class="custom-chk-rdb" rel="tooltip" title="{{ trans('textos.efecto_on_off') }}">
									<input type="checkbox" name="effect" value="{{ $ef->id }}" class="chkEffect"><span class="chk"></span>
								</label>

								{{-- Editar --}}
								<a target="_blank" class="btn btn-xs btn-info" href="{{ route('effects.edit', $ef->id) }}" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}">
									<i class="fa fa-edit"></i>
								</a>
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
	//Tabla RemittanceEffects:
    if($('#tblRemittanceEffects').length > 0){
    	//Filters:
        $('#tblRemittanceEffects thead tr').clone(true).appendTo('#tblRemittanceEffects thead');
        $('#tblRemittanceEffects thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 7){
                $(this).html('');
            }else{
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
            }
     
            $('input', this).on('keyup change', function(){
                if(tableRemittanceEffects.column(i).search() !== this.value){
                    tableRemittanceEffects
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });

        var tableRemittanceEffects = $('#tblRemittanceEffects').DataTable();
    }
});
</script>
@endpush

@endsection
@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-sm-12">
		<a href="{{ route('remittances.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_remesas') !!}
		</a>
	</div>
</div>

<div class="row">
	<div class="col-xs-12 col-sm-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('remittances.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}
					<input type="hidden" name="type" value="{{ $type }}">

					<p class="text-warning">{{ trans('textos.efectos_selec_aviso') }}</p>

					<div class="form-group row">
						{{-- Cuenta bancaria --}}
						<label for="bank_account" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cuenta_bancaria')) !!}*</label>
						<div class="col-lg-4">
							<x-select name="bank_account" class="select2" :options="$bank_accounts" label="cuenta_selec" required/>
						</div>

						{{-- Patrones --}}
						@if(count($patterns) == 1)
							<input type="hidden" name="pattern_id" value="{{ key($patterns) }}">

						@elseif(count($patterns) > 1)
							<label for="pattern_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.patron')) !!}</label>
							<div class="col-lg-4">
								<x-select name="pattern_id" class="select2" :options="patterns" label="patron_selec"/>
							</div>	
						@endif
					</div>	 

					<div id="effects">
						{{-- Se almacenan los effects que se vayan seleccionando --}}
					</div>
		
					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/> 				
				</form>
			</div>
		</div>
	</div>
</div>

<div class="row mb-3">
	<div class="table-responsive">
		<table class="table table-hover dTable" id="tblAddEffectsToRemittance">
			<thead>
				<th class="text-center wdt50">#</th>
				<th class="wdt100">
					@if($type == 'collection')
						{!! ucfirst(trans('textos.cliente')) !!}
					@else
						{!! ucfirst(trans('textos.proveedor')) !!}
					@endif
				</th>
				<th class="wdt50">{!! ucfirst(trans('textos.factura')) !!}</th>
				<th class="text-center wdt50">{!! ucfirst(trans('textos.fecha')) !!}</th>
				<th class="text-center wdt50">{!! ucfirst(trans('textos.importe')) !!}</th>
				<th class="text-center wdt50">{!! ucfirst(trans('textos.tipo')) !!}</th>
				<th class="text-center wdt100">{!! ucfirst(trans('textos.cuenta_bancaria')) !!}</th>
				<th class="text-center wdt50">{!! ucfirst(trans('textos.acciones')) !!}</th>
			</thead>

			<tbody>
				@foreach($effects as $ef)
					<tr>
						{{-- Número --}}
						<td class="text-right">
							@if($ef->effect_num) 
								<a target="_blank" href="{{ route('effects.edit', $ef->id) }}">{{ $ef->effect_num }}</a>
							@endif
						</td>
						{{-- Cliente o proveedor --}}
						<td>
							{{ $ef->company }}
						</td>
						{{-- Factura --}}
						<td class="text-right">
							@php
							$invoices = $ef->invoices;
            				$k = 0;
            				$html = '';
				            foreach($invoices as $inv){
				                if($k > 0) $html .= ', ';
				                $num_ = $inv->invoice_num? $inv->invoice_num:($inv->invoice_old_num? $inv->invoice_old_num:$inv->id);
				                $html .= '<a target="_blank" href="'.route('invoices.edit', $inv->id).'">'.$num_.'</a>';
				            }
				            @endphp

				            {!! $html !!}
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
							{{ $ef->account }}	
						</td>
						{{-- Acciones --}}
						<td class="text-right">
							{{-- Vincular a remesa --}}
							<label class="custom-chk-rdb" rel="tooltip" title="{{ trans('textos.efecto_on_off') }}">
								<input type="checkbox" name="effect" value="{{ $ef->id }}" class="chkEffect">
								<span class="chk"></span>
							</label>

							{{-- Editar --}}
							<a target="_blank" class="btn btn-xs btn-info" href="{{ route('effects.edit', $ef->id) }}" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}">
								<i class="fa fa-edit"></i>
							</a>
						</td>
					</tr>
				@endforeach	
			</tbody>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla AddEffectsToRemittance:
    if($('#tblAddEffectsToRemittance').length > 0){
    	//Filters:
        $('#tblAddEffectsToRemittance thead tr').clone(true).appendTo('#tblAddEffectsToRemittance thead');
        $('#tblAddEffectsToRemittance thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 7){
                $(this).html('');
            }else{
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
            }
     
            $('input', this).on('keyup change', function(){
                if(tableAddEffectsToRemittance.column(i).search() !== this.value){
                    tableAddEffectsToRemittance
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });

        var tableAddEffectsToRemittance = $('#tblAddEffectsToRemittance').DataTable();
    }
});
</script>
@endpush

@endsection
<div class="row">
	<div class="col-md-2">
        <div class="widget bg-warning no-padding">
            <div class="p-m">
            	<h3 class="font-bold mb-3 text-center">
                    {{ trans('textos.importe_venta') }}
                </h3>
                <h1 class="m-xs text-center">{{ $sale_amount['total_amount'] }}{{ $currency->symbol }}</h1>
            </div>
        </div>

        <div class="text-right mt-4">
	        {{-- Editar pedido --}}
	        <a href="{{ route('orders.edit', $sat->sale_id) }}" class="btn btn-info">
				<i class="fa fa-edit"></i>
				{!! trans('textos.venta_editar') !!}
			</a>
			<br>

			{{-- Add item --}}
			@can('sat.edit')
				<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-add-item">
					<i class="fa fa-plus"></i>
					{!! trans('textos.informacion_add') !!}
				</a>
			@endcan
		</div>
    </div>

    <div class="col-md-8 pt-2">
    	{{-- Tracking --}}
    	<h3 class="text-center mb-4">Tracking</h3>

    	@if($items->count())
    		@php
			$i = 0;
			@endphp

    		@foreach($items as $item)
    			<div class="timeline-item">
					<div class="row">
						<div class="col-sm-3 col-md-2 date_">
							{{-- Eliminar comentario --}}
							<a href="{{ route('staff-pick-observations.destroy', $item->id) }}" class="btn-confirm" data-msg="{{ trans('textos.comentario_confirm_eliminar') }}" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}">
								<i class="fa fa-trash text-danger"></i>
							</a>

							{{-- Editar comentario --}}
							<a href="#" class="btnEditInformation" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}" style="right: 30px;">
								<i class="fa fa-edit"></i>
							</a>

							{{ $item->created_at->format($languages[session()->get('locale')][4].' H:i:s'); }}
						</div>

						<div class="col-sm-7 col-md-10 content @if($i==0) no-top-border @endif">
							<p>{!! $item->observations !!}</p>
						</div>
					</div>
				</div>

				@php
				$i++;
				@endphp	
    		@endforeach
    	@else
    		<h3 class="text-warning text-center mt-5">{{ trans('textos.informaciones_sin_hasta_momento') }}</h3>
    	@endif
    </div>
</div>




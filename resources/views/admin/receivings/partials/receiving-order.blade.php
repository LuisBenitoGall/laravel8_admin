<div class="row">
	{{-- Controls  --}}
	<div class="col-12">
		{{-- Editar pedido --}}
		<a target="_blank" href="{{ route('orders.edit', $ord_->id) }}" class="btn btn-info">
			<i class="fa fa-edit"></i>
			{!! trans('textos.pedido_editar') !!}
		</a>	

		{{-- Recepcionar pedido completo --}}
		<a target="_blank" href="{{ route('receiving.all-order', $ord_->id) }}" class="btn btn-warning btn-confirm" data-msg="{{ trans('textos.recepcion_pedido_completo_aviso') }}">
			<i class="fa fa-sign-in-alt"></i>
			{!! trans('textos.recepcion_pedido_completo') !!}
		</a>
	</div>

	{{-- Líneas del pedido --}}
	<div class="col-12 mt-4">
		<table class="table modal-table">
			<thead>
				<tr>
					<th>{{ ucfirst(trans('textos.producto')) }}</th>
					<th>{{ ucfirst(trans('textos.ref_interna')) }}</th>
					<th class="text-center">{{ trans('textos.cantidad') }}</th>
					<th class="text-center">{{ trans('textos.precio') }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.cantidades_anteriores')) }}</th>
					<th class="text-center wdt200">{{ ucfirst(trans('textos.cantidad_pendiente')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.acciones')) }}</th>
				</tr>
			</thead>

			<tbody>
				{{-- Items --}}
				@foreach($items as $item)
					@if($item->order_id == $ord_->id)
						<tr>
							{{-- Producto --}}
							<td>{{ $item->concept }}</td>
							{{-- Referencia interna --}}
							<td>
								@if($item->internal_id)
									<a target="_blank" href="{{ route('products.edit', $item->internal_id) }}">
								@endif
									{{ $item->internal_ref }}
								@if($item->internal_id)
									</a>
								@endif
							</td>
							{{-- Cantidad del pedido --}}
							<td class="text-right">{{ $item->qty }}</td>
							{{-- Precio --}}
							<td class="text-right">{{ $item->price }}{{ $currency->symbol }}</td>
							{{-- Cantidades recibidas --}}
							<td class="text-right">{{ $item->prev_qty }}</td>
							{{-- Cantidad pendiente --}}
							<td class="text-right">
								@php
								$pendent_qty = $item->qty - $item->prev_qty;
								@endphp

								@if($pendent_qty > 0)
									<input type="text" class="form-control-sm text-right set-decimals pendent-qty" value="{{ $pendent_qty }}">
									<a href="#" class="btn btn-success btn-circle btn-circle-mini btnSaveQty" data-id="{{ $item->id }}">
										<i class="fa fa-check"></i>
									</a>
								@else
									{{-- Línea ya recibida --}}
									<input type="text" class="form-control-sm text-right" value="{{ $pendent_qty }}" disabled>
									<a href="{{ route('delivery-items.destroy', [$delivery->id, $item->id]) }}" class="btn btn-danger btn-circle btn-circle-mini btnDeleteItem btn-confirm" data-id="{{ $item->id }}" data-msg="{{ trans('textos.recepcion_linea_eliminar_confirm') }}">
										<i class="fa fa-times"></i>
									</a>
								@endif
							</td>
							<td class="text-right">
									
							</td>
						</tr>
					@endif
				@endforeach	
			</tbody>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	var delivery_id = {{ $delivery->id }};

	//Guardar cantidad recepcionada:
	$(document).on('click', '.btnSaveQty', function(){
		var elem = $(this);
		var qty = elem.prev('.pendent-qty').val();
		var id = elem.data('id');

		$.ajax({
            headers:{
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data:{
                _token:$('meta[name="csrf-token"]').attr('content'),
                delivery_id:delivery_id, 
                id:id, 
                qty:qty
            },
            type: 'POST',
            url: '/admin/delivery-items/store',
            success: function(response){
            	if(response){
            		elem.removeClass('btn-success btnSaveQty').addClass('btn-danger btnDeleteQty').html('<i class="fa fa-times"></i>');



            		//console.log(response);
            		//var btn_delete = '<a href="#" class="btn btn-danger btn-circle btn-circle-mini btnSaveQty" rel="tooltip" title="'+txtEliminar+'" data-id=""><i class="fa fa-times"></i></a>';
            		//elem.prev().html(btn_delete);
            		//elem.remove();

            		//$('*[data-id="'+response.item_id+'"]').remove;

	            }else{
	                elem.parent('label').before('<i class="fa fa-exclamation-triangle check-ko"></i>');
	            }
            }
        });
	});

	//Eliminar línea de recepción:
	$(document).on('click', '.btnDeleteItem', function(){
		var elem = $(this);

	});
});
</script>
@endpush
	
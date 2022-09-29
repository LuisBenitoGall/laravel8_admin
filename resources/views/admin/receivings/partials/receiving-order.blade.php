<div class="row">
	{{-- Column left --}}
	<div class="col-md-2">
		{{-- Editar pedido --}}
		<a target="_blank" href="{{ route('orders.edit', $ord_->id) }}" class="btn btn-info">
			<i class="fa fa-edit"></i>
			{!! trans('textos.pedido_editar') !!}
		</a>	
	</div>

	{{-- Column right --}}
	<div class="col-md-10">
		<table class="table">
			<thead>
				<tr>
					<td>{{ ucfirst(trans('textos.producto')) }}</td>
					<td class="text-center">{{ trans('textos.cantidad') }}</td>
					<td class="text-center">{{ trans('textos.precio') }}</td>
					<td class="text-center">{{ ucfirst(trans('textos.acciones')) }}</td>
				</tr>
			</thead>

			<tbody>
				{{-- Items --}}
				@foreach($items as $item)
					@if($item->order_id == $ord_->id)
						<tr>
							<td>{{ $item->concept }}</td>
							<td class="text-right">{{ $item->qty }}</td>
							<td class="text-right">{{ $item->price }}{{ $currency->symbol }}</td>
							<td class="text-right">
								
							</td>
						</tr>
					@endif
				@endforeach	
			</tbody>
		</table>

		
	</div>
</div>




	
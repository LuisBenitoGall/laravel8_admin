<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dTable" id="tblProjectOrders">
			<thead>
				<tr>
					<th class="text-center wdt100">#</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.numero_antiguo')) !!}</th>
					<th class="wdt150">{!! ucfirst(trans('textos.pedido')) !!}</th>
					<th class="wdt100">{!! ucfirst(trans('textos.presupuesto')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.importe')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.entrega_prevista')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.fecha_entrega')) !!}</th>
					<th class="wdt100">{!! ucfirst(trans('textos.estado')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>

			<tbody>
				@php
				$total_amount = 0;
				@endphp

				@foreach($project_orders as $ord)
					@if(in_array($ord->id, $orders_))
						@php
						$total_amount = $total_amount + $ord->amount;
						@endphp

						<tr>
							{{-- Pedido --}}
							<td class="text-right">
								<a target="_blank" href="{{ route('orders.edit', $ord->id) }}"> 
									@if($ord->order_num)
										{{ $ord->order_num }}	
									@else
										{{ $ord->id }}
									@endif
								</a>
							</td>
							<td class="text-right">{{ $ord->order_old_num }}</td>
							{{-- Título --}}
							<td>{{ $ord->name }}</td>	
							{{-- Presupuesto --}}
							<td class="text-right">
								@if($ord->is_budget)
									<a target="_blank" href="{{ route('budgets.edit', $ord->id) }}"> 
										@if($ord->budget_num)
											{{ $ord->budget_num }}	
										@else
											{{ $ord->id }}
										@endif
									</a>
								@endif
							</td>
							{{-- Importe --}}
							<td class="text-right">{{ $ord->amount }}{{ $currency->symbol }}</td>
							{{-- Entrega prevista --}}
							<td class="text-right">{{ $ord->fechaEntregaPrevista }}</td>
							{{-- Fecha entrega --}}
							<td class="text-right">{{ $ord->fechaEntrega }}</td>
							{{-- Estado --}}
							<td>{{ $ord->order_status }}</td>
							{{-- Acciones --}}
							<td class="text-right">
								{{-- Editar --}}
								<a target="_blank" href="{{ route('orders.edit', $ord->id) }}" class="btn btn-xs btn-info btn-module" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>

								{{-- Eliminar --}}
								<form method="POST" action="{{ route('project-orders.destroy') }}" class="frm-inline" role="form" accept-charset="UTF-8">
								    <input name="_method" type="hidden" value="DELETE">
								    {{ csrf_field() }}
								    <input type="hidden" name="project" value="{{ $project->id }}">
								    <input type="hidden" name="order" value="{{ $ord->id }}">
								    <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.desvincular')) }}" data-msg="{{ ucfirst(trans('textos.proyecto_pedido_confirm_desvincular')) }}"><i class="fa fa-trash"></i></button>
								</form>
							</td>
						</tr>
					@endif
				@endforeach
			</tbody>

			<tfoot>
				<tr>
					<td colspan="4" class="text-right">
						{{ ucfirst(trans('textos.importe')) }}
					</td>
					<td class="text-right">
						{{ $total_amount }}{{ $currency->symbol }}	
					</td>
					<td colspan="4"></td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>
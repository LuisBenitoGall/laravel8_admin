<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dTable" id="tblProjectBudgets">
			<thead>
				<tr>
					<th class="text-center wdt100">#</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.numero_antiguo')) !!}</th>
					<th class="wdt150">{!! ucfirst(trans('textos.presupuesto')) !!}</th>
					<th class="wdt100">{!! ucfirst(trans('textos.pedido')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.importe')) !!}</th>
					<th class="wdt100">{!! ucfirst(trans('textos.modalidad')) !!}</th>
					<th class="wdt200">{!! ucfirst(trans('textos.responsable')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
					<th class="wdt100">{!! ucfirst(trans('textos.estado')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>

			<tbody>
				@php
				$total_amount = 0;
				@endphp

				@foreach($project_orders as $bo)
					@if(in_array($bo->id, $budgets_))
						@php
						$total_amount = $total_amount + $bo->amount;
						@endphp
					
						<tr>
							{{-- Presupuesto --}}
							<td class="text-right">
								<a target="_blank" href="{{ route('budgets.edit', $bo->id) }}"> 
									@if($bo->budget_num)
										{{ $bo->budget_num }}	
									@else
										{{ $bo->id }}
									@endif
								</a>
							</td>
							<td class="text-right">{{ $bo->budget_old_num }}</td>
							<td>{{ $bo->name }}</td>
							<td class="text-right">
								@if($bo->id)
									<a target="_blank" href="{{ route('orders.edit', $bo->id) }}"> 
										@if($bo->order_num)
											{{ $bo->order_num }}	
										@else
											{{ $bo->id }}
										@endif
									</a>
								@endif
							</td>
							{{-- Importe --}}
							<td class="text-right">{{ $bo->amount }}{{ $currency->symbol }}</td>
							<td>{{ $bo->modalidad }}</td>
							<td>{{ $bo->nombre }} {{ $bo->surname }}</td>
							<td class="text-right">{{ $bo->alta }}</td>
							{{-- Estado --}}
							<td>{{ $bo->budget_status }}</td>
							{{-- Acciones --}}
							<td class="text-right">
								{{-- Editar --}}
								<a target="_blank" href="{{ route('budgets.edit', $bo->id) }}" class="btn btn-xs btn-info btn-module" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>

								{{-- Eliminar --}}
								<form method="POST" action="{{ route('project-orders.destroy') }}" class="frm-inline" role="form" accept-charset="UTF-8">
								    <input name="_method" type="hidden" value="DELETE">
								    {{ csrf_field() }}
								    <input type="hidden" name="project" value="{{ $project->id }}">
								    <input type="hidden" name="order" value="{{ $bo->id }}">
								    <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.desvincular')) }}" data-msg="{{ ucfirst(trans('textos.proyecto_presupuesto_confirm_desvincular')) }}"><i class="fa fa-trash"></i></button>
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
					<td colspan="5"></td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>
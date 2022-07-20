{{-- Debe estar declarado como presupuesto --}}
@if($order->is_budget)
	<label class="mr-5">
		{{ ucfirst(trans('textos.presupuesto')) }}:	
		<b>
			<a href="{{ route('budgets.edit', $order) }}" class="text-warning">
				@if($order->budget_num) {{ $order->budget_num }} @else {{ $order->id }} @endif
			</a>
		</b>
	</label>
@endif
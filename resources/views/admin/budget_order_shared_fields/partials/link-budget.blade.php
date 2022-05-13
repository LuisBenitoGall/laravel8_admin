@if($budget)
	<label class="mr-5">
		{{ ucfirst(trans('textos.presupuesto')) }}:	
		<b>
			<a href="{{ route('budgets.edit', $budget) }}" class="text-warning">
				@if($budget->budget_num) {{ $budget->budget_num }} @else {{ $budget->id }} @endif
			</a>
		</b>
	</label>
@endif
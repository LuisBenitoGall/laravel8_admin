@php
$num = $budget->budget_num? $budget->budget_num:$budget->id;
@endphp

@if($from == 'customers')
	<a href="{{ route('budgets.edit', $budget) }}">{{ $num }}</a>
@else
	<a href="{{ route('budgets.show', $budget) }}">{{ $num }}</a>
@endif
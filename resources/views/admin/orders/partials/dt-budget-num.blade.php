@php
$num = !$row->is_budget? false:($row->budget_num? $row->budget_num:$row->id);
@endphp

@if($num)
	<a href="{{ route('budgets.edit', $row) }}">{{ $num }}</a>
@endif


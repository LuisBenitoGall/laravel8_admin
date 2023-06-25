@php
$num = $row->budget_num? $row->budget_num:$row->id;
@endphp

@if($from == 'customers')
	<a href="{{ route('budgets.edit', $row) }}">{{ $num }}</a>
@else
	<a href="{{ route('budgets.show', $row) }}">{{ $num }}</a>
@endif
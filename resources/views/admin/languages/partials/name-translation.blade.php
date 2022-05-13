@if($row->translations != NULL)
	@php
	$trans = unserialize($row->translations);
	@endphp
	
	@if(isset($trans[session()->get('locale')]) && $trans[session()->get('locale')] != '') 
		{{ ucfirst($trans[session()->get('locale')]) }}	
	@else
		{{ ucfirst($row->name) }}
	@endif
@else
	{{ ucfirst($row->name) }}
@endif
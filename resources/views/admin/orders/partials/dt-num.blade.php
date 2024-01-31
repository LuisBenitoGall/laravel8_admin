@php
if($row->provider_id == session('currentCompany')){
	$num = $row->order_num? $row->order_num:($row->order_old_num? $row->order_old_num:$row->id);
}else{
	$num = $row->purchase_num? $row->purchase_num:($row->purchase_old_num? $row->purchase_old_num:$row->id);	
}
@endphp

<a href="{{ route('orders.edit', $row) }}">{{ $num }}</a>

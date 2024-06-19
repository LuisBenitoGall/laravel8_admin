@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
        @can('payment-methods.create')
		    <a href={{ route('payment-methods.create') }} class="btn btn-info"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.metodo_nuevo')) !!}</a>
        @endcan
    </div>
</div>

<div class="row">
    <div class="table-responsive">
    	<table class="table table-hover dtabla" id="tblPaymentMethods">
    		<thead>
    			<tr>
    				<th>{!! ucfirst(trans('textos.metodo')) !!}</th>
    				<th>{!! ucfirst(trans('textos.descripcion')) !!}</th>
    				<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
    			</tr>
    		</thead>
    	</table>
    </div>
</div>

@push('scripts')
<script>
$(function(){
    //Tabla Payment Methods:
    if($('#tblPaymentMethods').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');   
        
        var tablePaymentMethods = $('#tblPaymentMethods').DataTable({
            serverSide: true,
            processing: true, 
            ajax: {
                "type": "GET",
                "url": "/api/admin/payment-methods",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'description'},
                {data: 'acciones', className: 'text-right'}
            ] 
        });
    }
});
</script>
@endpush

@endsection
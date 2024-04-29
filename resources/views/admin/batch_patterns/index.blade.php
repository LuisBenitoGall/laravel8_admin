@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('batch-patterns.create')
			<a class="btn btn-info" href="{{ route('batch-patterns.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.patron_nuevo')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblBatchPatterns">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.nombre')) !!}</th>
					<th>{!! ucfirst(trans('textos.patron')) !!}</th>
					<th>{!! ucfirst(trans('textos.reinicio_anual')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Batch Patterns:
    if($('#tblBatchPatterns').length > 0){
        $('#tblBatchPatterns').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/batch-patterns",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', className: 'capitalize'},
                {data: 'patron'},
                {data: 'reset', className: 'text-center'},
                {data: 'alta', className: 'text-right'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }
});
</script>
@endpush

@endsection
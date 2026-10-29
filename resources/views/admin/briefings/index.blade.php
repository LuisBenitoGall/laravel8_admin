@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
    {{-- Acciones: --}}
	<div class="col-12">
		{{-- Nuevo briefing --}}
		@can('briefings.create')
    		<a href="{{ route('briefings.create') }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.briefing_nuevo')) !!}
    		</a>
        @endcan
    </div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblBriefings">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.briefing')) !!}</th>
                    <th>{!! ucfirst(trans('textos.cliente')) !!}</th>
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
	//Tabla Briefings:
    if($('#tblBriefings').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblBriefings').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/briefings",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'customer'},
                {data: 'date', className: 'text-right'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }
});
</script>
@endpush

@endsection
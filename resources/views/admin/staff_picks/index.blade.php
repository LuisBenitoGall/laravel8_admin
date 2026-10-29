@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('staff-pick.create')
    		<a href="{{ route('staff-pick.create') }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.proceso_nuevo')) !!}
    		</a>
        @endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblStaffPicks">
			<thead>
				<tr>
					<td>{{ ucfirst(trans('textos.proceso')) }}</td>
					<td class="text-center">{{ ucfirst(trans('textos.publicado_desde')) }}</td>
					<td class="text-center">{{ ucfirst(trans('textos.publicado_hasta')) }}</td>
					<td class="text-center">{{ ucfirst(trans('textos.candidatos')) }}</td>
					<td class="text-center">{{ ucfirst(trans('textos.acciones')) }}</td>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Staff Picks:
    if($('#tblStaffPicks').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblStaffPicks').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/staff-picks",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', className: 'capitalize'},
                {data: 'published_at', className: 'text-right'},
                {data: 'published_end', className: 'text-right'},
                {data: 'candidates', className: 'text-right'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }
});
</script>
@endpush

@endsection
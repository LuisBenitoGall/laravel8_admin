@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Volver a agendas --}}
		<a href="{{ route('schedules.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_agendas') !!}
		</a>

		{{-- Crear tipo de evento --}}
		@can('schedule.edit')
    		<a href="{{ route('schedule-event-types.create') }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.evento_tipo_nuevo')) !!}
    		</a>
        @endcan
    </div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblScheduleEventTypes">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.evento')) !!}</th>
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
	//Tabla Schedule Event Types:
    if($('#tblScheduleEventTypes').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblScheduleEventTypes').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/schedule-event-types",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'event'},
                {data: 'description'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }
});
</script>
@endpush

@endsection
@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('briefing-templates.create')
    		<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-new-template">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.plantilla_nueva')) !!}
    		</a>
        @endcan
    </div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblBriefingTemplates">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.plantilla')) !!}</th>
                    <th>{!! ucfirst(trans('textos.condiciones')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
                	<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

{{-- Modals: --}}
{{-- New template: --}}
@include('admin.briefing_templates.partials.modal-new-template')

@push('scripts')
<script>
$(function(){
	//Tabla Briefing Templates:
    if($('#tblBriefingTemplates').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblBriefingTemplates').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/briefing-templates",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'conditions'},
                {data: 'date', className: 'text-right'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }
});
</script>
@endpush

@endsection

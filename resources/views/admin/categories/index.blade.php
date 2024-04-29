@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="moduleId" value="{{ $module->id }}">

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can($permission.'.edit')
			<a class="btn btn-info" href="{{ route('categories.create', $module->id) }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.categoria_nueva')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblCategories">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.categoria')) !!}</th>
					<th>{!! ucfirst(trans('textos.nivel')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Categories:
    if($('#tblCategories').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var module_id = $('#moduleId').val();

        var tableCategories = $('#tblCategories').DataTable({
            serverSide: true,
            processing: true,
            language: {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
            },
            ajax: {
                "type": "GET",
                "url": "/api/admin/categories/"+module_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', name: 'name'},
                {data: 'level', name: 'level'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }
});
</script>
@endpush

@endsection
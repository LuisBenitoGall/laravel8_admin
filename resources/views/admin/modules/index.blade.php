@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('modules.create')
			<a href="{{ route('modules.create') }}" class="btn btn-info">
				<i class="fa fa-plus"></i>
				{!! ucfirst(trans('textos.modulo_nuevo')) !!}
			</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblModules">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.modulo')) !!}</th>
					<th>{!! ucfirst(trans('textos.nivel')) !!}</th>
	                <th class="wdt50">{!! ucfirst(trans('textos.icono')) !!}</th>
	                <th class="wdt50">{!! ucfirst(trans('textos.color')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div> 
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Modules:
    if($('#tblModules').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblModules').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/modules",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'module', className: 'capitalize'},
                {data: 'level'},
                {data: 'icon', className: 'text-center'},
                {data: 'color', className: 'text-center'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }
});
</script>
@endpush

@endsection
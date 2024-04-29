@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		@can('accounting-concepts.create')
			<a class="btn btn-info" href="{{ route('accounting-concepts.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.concepto_nuevo')) !!}</a>
		@endcan

		{{-- Exportar valores --}}
        @can('accounting-concepts.edit')
            @if(count(session('companies')) > 1)
                <a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-export-values">
                    <i class="fa fa-file-export"></i> {!! ucfirst(trans('textos.exportar_valores')) !!} 
                </a>
            @endif
        @endcan
	</div>	
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblAccountingConcepts">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.concepto')) !!}</th>
					<th>{!! ucfirst(trans('textos.nivel')) !!}</th>
					<th>{!! ucfirst(trans('textos.descripcion')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

{{-- Modals --}}
{{-- Export values --}}
@include('admin.partials.modal-export-values', ['action' => 'accounting-concepts.export'])

@push('scripts')
<script>
$(function(){
	//Tabla Accounting Concepts:
    if($('#tblAccountingConcepts').length > 0){
        $('#tblAccountingConcepts').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/accounting-concepts",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'level'},
                {data: 'description'},
                {data: 'acciones', name: 'acciones', className: 'text-right'}
            ]
        });
    }
});
</script>
@endpush

@endsection
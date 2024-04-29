@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones --}}
	<div class="col-12">
		@can('accounts.create')
			<a class="btn btn-info" href="{{ route('accounts.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.cuenta_nueva')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblAccounts">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.cuenta')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.tarifa')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.duracion')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Accounts:
    if($('#tblAccounts').length > 0){
        $('#tblAccounts').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/accounts",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'rate', className: 'text-right'},
                {data: 'duration', className: 'text-right'},
                {data: 'acciones', name: 'acciones', className: 'text-right'}
            ]
        });
    }
});
</script>
@endpush

@endsection
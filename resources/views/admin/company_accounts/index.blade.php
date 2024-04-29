@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('companies.edit')
			<a href="{{ route('company-accounts.create') }}" class="btn btn-info"><i class="fa fa-shopping-cart"></i> {!! ucfirst(trans('textos.cuenta_renovar')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblCompanyAccounts">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.cuenta')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.inicio')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fin')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.precio')) !!}</th>
					<th>{!! ucfirst(trans('textos.estado')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Company Accounts:
    if($('#tblCompanyAccounts').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblCompanyAccounts').DataTable({
            serverSide: true,
            /*processing: true,*/
            ajax: {
                "type": "GET",
                "url": "/api/admin/company-accounts",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'inicio', className: 'text-right'},
                {data: 'fin', className: 'text-right'},
                {data: 'price', className: 'text-right'},
                {data: 'status', className: 'text-center'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
                {"targets": 3,
                "render": function(data, type, row){
                    return row['price']+row['currency'];
                }
                },
                {"targets": 4,
                "render": function(data, type, row){
                    if(row['status'] == '0'){
                        return '<i class="fa fa-exclamation-circle text-warning" rel="tooltip" title="'+txtCuentaInactiva+'"></i>';
                    }else if(row['status'] == 1){
                        return '<i class="fa fa-check text-success" rel="tooltip" title="'+txtCuentaActiva+'"></i>';
                    }else if(row['status'] == 2){
                        return '<i class="fa fa-ban text-danger" rel="tooltip" title="'+txtCuentaCancelada+'"></i>';
                    }
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    //Eliminar:
                    var eliminar = '';
                    if(row['status'] == 1 && row['eliminar']){
                        eliminar += '<form method="POST" action="company-accounts/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtCuentaEliminar+'" data-msg="'+txtCuentaConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }
                    return eliminar;
                }
                }
            ]
        });
    }
});
</script>
@endpush

@endsection
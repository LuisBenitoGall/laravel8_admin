@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones --}}
	<div class="col-12">
		@can('employees.create')
			<a class="btn btn-info" href="{{ route('users.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.empleado_nuevo')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblEmployees">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.empleado')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.inicio')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fin')) !!}</th>
					<th>{!! ucfirst(trans('textos.puesto')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.contrato_estado')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Employees:
    if($('#tblEmployees').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        //Filters:
        $('#tblEmployees thead tr').clone(true).appendTo('#tblEmployees thead');
        $('#tblEmployees thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
     
            $('input', this).on('keyup change', function(){
                if(tableEmployees.column(i).search() !== this.value){
                    tableEmployees
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });
        
        var tableEmployees = $('#tblEmployees').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/employees",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'empleado'},
                {data: 'inicio', className: 'text-right'},
                {data: 'fin', className: 'text-right'},
                {data: 'job_title'},
                {data: 'id', className: 'text-center'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
                {"targets": 4,
                "render": function(data, type, row){
                    var status = '';
                    if(row['status'] == 0){
                        status = '<i class="fa fa-exclamation"></i>';
                    }else if(row['status'] == 1){
                        status = '<i class="fa fa-check text-success"></i>';    
                    }else{
                        status = '<i class="fa fa-exclamation text-warning"></i>';
                    }   

                    return status; 
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    //Editar:
                    var editar = '';
                    if(row['editar']){
                        editar = '<a href="/admin/employees/'+row['id']+'/edit" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtContratoEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    return editar;
                }
                }
            ]
        });
    }
});
</script>
@endpush

@endsection
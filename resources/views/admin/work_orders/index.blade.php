@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="dtController" value="work-orders">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Crear orden de trabajo --}}
		@can('work-orders.create')
			<a href="{{ route('work-orders.create') }}" class="btn btn-info">
				<i class="fa fa-plus"></i>
				{!! trans('textos.trabajo_orden_nueva') !!}
			</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblWorkOrders">
			<thead>
				<tr>
					<th class="text-center">{!! ucfirst(trans('textos.trabajo_orden')) !!}</th>
					<th>{!! ucfirst(trans('textos.titulo')) !!}</th>
					<th class="text-center wdt150">{!! ucfirst(trans('textos.entrega_prevista')) !!}</th>
					<th class="text-center wdt150">{!! ucfirst(trans('textos.fecha_entrega')) !!}</th>
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
	//Tabla Work Orders:
    if($('#tblWorkOrders').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        //Filters:
        $('#tblWorkOrders thead tr').clone(true).appendTo('#tblWorkOrders thead');
        $('#tblWorkOrders thead tr:eq(1) th').each(function(i){
            var title = $(this).text(); 
            if(i == 5){
                $(this).html('');
            }else{
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
                // aquí el código retirado
                
            }

            $('input', this).on('keyup change', function(){
                if(tableWorkOrders.column(i).search() !== this.value){
                    tableWorkOrders
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });
        
        var tableWorkOrders = $('#tblWorkOrders').DataTable({
            serverSide: true,
            /*processing: true,*/
            drawCallback: function(){
                $('.select2-dt').select2();
            },
            ajax: {
                "type": "GET",
                "url": "/api/admin/work-orders",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'id', className: 'text-right'},
                {data: 'name'},
                {data: 'fechaEntregaPrevista', className: 'text-right'}, 
                {data: 'fechaEntrega', className: 'text-right'},
                {data: 'estado'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
                {"targets": 0,
                "render": function(data, type, row){
                    var num = row['work_order_num']? row['work_order_num']:row['id'];
                    var norder = '<a target="_blank" href="/admin/work-orders/'+row['id']+'/edit">'+num+'</a>';
                    return norder;
                }
                },
                {"targets": -2,
                "render": function(data, type, row){
                    return '<span class="xoption-'+row['id']+'">'+row['estado']+'</span>'; 
                }
                }, 
                {"targets": -1,
                "render": function(data, type, row){
                    //Estado múltiple:
                    var estado = '';
                    if(row['editar']){
                        estado = multipleStatus(row['estados'], row['status'], row['id']);
                    }

                    //Editar:
                    var editar = '';
                    if(row['editar']){
                        editar = '<a href="work-orders/'+row['id']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['eliminar']){
                        //Si el status de la orden permite eliminarla:
                        if(row['allow_delete']){
                            eliminar = '<form method="POST" action="work-orders/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                                eliminar += '<input name="_method" type="hidden" value="DELETE">';
                                eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                                eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtTrabajoOrdenConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                            eliminar += '</form>';
                        }
                    }

                    return estado+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }
});
</script>
@endpush

@endsection
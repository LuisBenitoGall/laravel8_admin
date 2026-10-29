<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblNotificationsRead" >
			<thead>
				<tr>
					<th class="wdt250">{!! ucfirst(trans('textos.titulo')) !!}</th>
					<th class="wdt100">{!! ucfirst(trans('textos.autor')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.fecha')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.leido')) !!}</th>
					<th class="text-center wdt150">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Notifications Read:
    if($('#tblNotificationsRead').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        //Filters:
        $('#tblNotificationsRead thead tr').clone(true).appendTo('#tblNotificationsRead thead');
        $('#tblNotificationsRead thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 4){
                $(this).html('');
            }else{
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
            }
     
            $('input', this).on('keyup change', function(){
                if(tableNotificationsRead.column(i).search() !== this.value){
                    tableNotificationsRead
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });
        
        var tableNotificationsRead = $('#tblNotificationsRead').DataTable({
            "order": [[ 2, "desc" ]],
            serverSide: true,
            /*processing: true,*/
            ajax: {
                "type": "GET",
                "url": "/api/admin/notifications-read",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'id', className: 'capitalize'},
                {data: 'id'},
                {data: 'id', className: 'text-right'},
                {data: 'id', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
                {"targets": 0,
                "render": function(data, type, row){
                    return row['data']['title'];
                }
                },
                {"targets": 1,
                "render": function(data, type, row){
                    return row['data']['sender'];
                }
                },
                {"targets": 2,
                "render": function(data, type, row){
                    return '<span style="display: none">'+row['created_at']+'</span>'+row['alta'];
                }
                }, 
                {"targets": 3,
                "render": function(data, type, row){
                    return '<span style="display: none">'+row['read_at']+'</span>'+row['leido'];
                }
                },  
                {"targets": -1,
                "render": function(data, type, row){
                    //Leído:
                    var checked = (row['read_at'])? 'checked':'';
                    var read = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtLeidoOnOff+'">';
                        read += '<input type="checkbox" name="status" '+checked+' ><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="/admin/notifications/status"></span>';
                    read += '</label>';

                    //Ver:
                    var ver = '<a href="/admin/notifications/'+row['id']+'/show" class="btn btn-xs btn-info" rel="tooltip" title="'+txtVer+'"><i class="fa fa-eye"></i></a>';

                    //Eliminar:
                    var eliminar = '<form method="POST" action="/admin/notifications/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                        eliminar += '<input name="_method" type="hidden" value="DELETE">';
                        eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                        eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtNotificacionConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                    eliminar += '</form>';

                    return read+' '+ver+' '+eliminar;
                }
                }
            ]
        });
    }
});
</script>
@endpush

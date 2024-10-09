<div class="row">
	{{-- Equipo cliente --}}
	<div class="col-lg-6">
		<div class="row">
			<div class="col-12">
				<h4 class="text-center mb-3" style="height: 35px;">
					{{ trans('textos.cliente_equipo') }}

					@if($order->customer_id == session('currentCompany'))
						{{-- Debe estar activo el módulo de RRHH --}}
						@if($module_rrhh)
							<a href="#" class="btn btn-xs btn-warning ml-3 btnAddMember" data-toggle="modal" data-target="#modal-add-member-customer" rel="tooltip" title="{{ trans('textos.miembro_add') }}" data-company="{{ $order->customer_id }}" data-environment="customer">
								<i class="fa fa-plus"></i>
							</a>
						@else
							<p class="mt-2">
								<a target="_blank" href="{{ route('company-modules.index') }}" class="text-warning"><u>{{ trans('textos.modulo_rrhh_activar_aviso') }}</u></a>
							</p>
						@endif
					@endif
				</h4>
			</div>

			<div class="table-responsive">
				<table class="table table-hover dtabla" id="tblOrderCustomerTeam">
					<thead>
						<tr>
							<th>{!! ucfirst(trans('textos.usuario')) !!}</th>
							<th>{!! ucfirst(trans('textos.role')) !!}</th>
							<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
	</div>

	{{-- Equipo proveedor --}}
	<div class="col-lg-6">
		<div class="row">
			<div class="col-12">
				<h4 class="text-center mb-3" style="height: 35px;">
					{{ trans('textos.proveedor_equipo') }}

					@if($order->provider_id == session('currentCompany'))
						{{-- Debe estar activo el módulo de RRHH --}}
						@if($module_rrhh)
							<a href="#" class="btn btn-xs btn-warning ml-3 btnAddMember" data-toggle="modal" data-target="#modal-add-member-provider" rel="tooltip" title="{{ trans('textos.miembro_add') }}" data-company="{{ $order->provider_id }}" data-environment="provider">
								<i class="fa fa-plus"></i>
							</a>
						@else
							<p class="mt-2">
								<a target="_blank" href="{{ route('company-modules.index') }}" class="text-warning"><u>{{ trans('textos.modulo_rrhh_activar_aviso') }}</u></a>
							</p>
						@endif
					@endif
				</h4>
			</div>

			<div class="table-responsive">
				<table class="table table-hover dtabla" id="tblOrderProviderTeam">
					<thead>
						<tr>
							<th>{!! ucfirst(trans('textos.usuario')) !!}</th>
							<th>{!! ucfirst(trans('textos.role')) !!}</th>
							<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Order Customer Team:
    if($('#tblOrderCustomerTeam').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var order = $('#myOrder').val();

        var tableOrderCustomerTeam = $('#tblOrderCustomerTeam').DataTable({
            serverSide: true,
            processing: true,
            // drawCallback: function() {
            //     $('.select2-dt').select2();
            // },
            ajax: {
                "type": "GET",
                "url": "/api/admin/order-teams/"+order+"/customer",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'rol'},
                {data: 'id', className: 'text-right'}
            ]
            // columnDefs: [
            //     {"targets": 0,
            //     "render": function(data, type, row){
            //         if(row['Image']){
            //             var avatar = '<img src="/img/users/'+row['Image']+'" class="img-circle mr-2" width="36">';    
            //         }else{
            //             var avatar = setAvatar(row['name'], row['surname']);
            //         }
            //         return avatar+row['name']+' '+row['surname'];
            //     }
            //     },
            //     {"targets": -1,
            //     "render": function(data, type, row){
            //         //Mostrar:
            //         var show = '';
            //         if(row['users_show']){
            //             show = '<a target="_blank" href="/admin/users/'+row['id']+'/show" class="btn btn-xs btn-info" rel="tooltip" title="'+txtVer+'"><i class="fa fa-eye"></i></a>';
            //         }                    

            //         //Eliminar miembro del equipo:
            //         var eliminar = '';
            //         if(row['rol'] == '' && row['customers_on']){
            //             eliminar = '<form method="POST" action="/admin/orders/'+order+'/remove-member" class="frm-inline" role="form" accept-charset="UTF-8">';
            //                 eliminar += '<input name="_method" type="hidden" value="DELETE">';
            //                 eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
            //                 eliminar += '<input type="hidden" name="member" value="'+row['id']+'">';
            //                 eliminar += '<input type="hidden" name="environment" value="customer">';
            //                 eliminar += '<button type="submit" name="submit_member" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtMiembroConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
            //             eliminar += '</form>';
            //         }

            //         return show+' '+eliminar;    
            //     }
            //     }
            // ]
        });
    }

    //Tabla Order Provider Team:
    if($('#tblOrderProviderTeam').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var order = $('#myOrder').val();

        var tableOrderProviderTeam = $('#tblOrderProviderTeam').DataTable({
            serverSide: true,
            /*processing: true,*/
            // drawCallback: function() {
            //     $('.select2-dt').select2();
            // },
            ajax: {
                "type": "GET",
                "url": "/api/admin/order-teams/"+order+"/provider",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'rol'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
                {"targets": 0,
                "render": function(data, type, row){
                    if(row['Image']){
                        var avatar = '<img src="/img/users/'+row['Image']+'" class="img-circle mr-2" width="36">';    
                    }else{
                        //var avatar = setAvatar(row['name'], row['surname']);
                        var avatar = '';
                    }
                    return avatar+row['name']+' '+row['surname'];
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    //Mostrar:
                    var show = '';
                    if(row['users_show']){
                        show = '<a target="_blank" href="/admin/users/'+row['id']+'/show" class="btn btn-xs btn-info" rel="tooltip" title="'+txtVer+'"><i class="fa fa-eye"></i></a>';
                    }         

                    //Eliminar miembro del equipo:
                    var eliminar = '';
                    if(row['rol'] == '' && row['providers_on']){
                        eliminar = '<form method="POST" action="/admin/orders/'+order+'/remove-member" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<input type="hidden" name="member" value="'+row['id']+'">';
                            eliminar += '<input type="hidden" name="environment" value="provider">';
                            eliminar += '<button type="submit" name="submit_member" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtMiembroConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return show+' '+eliminar;    
                }
                }
            ]
        });
    }
});
</script>
@endpush
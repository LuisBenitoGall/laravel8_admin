@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
        @can('permissions.create')
		    <a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-new-permission"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.permiso_nuevo')) !!}</a>
        @endcan
    </div>
</div>

<div class="row">
    <div class="table-responsive">
    	<table class="table table-hover dtabla" id="tblPermissions" style="width: 100%;">
    		<thead>
    			<tr>
    				<th>{!! ucfirst(trans('textos.permiso')) !!}</th>
    				<th>{!! ucfirst(trans('textos.funcionalidad')) !!}</th>
    				<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
    			</tr>
    		</thead>
    	</table>
    </div>
</div>

{{-- Modals: --}}
{{-- New permission: --}}
<div class="modal fade modal-new-permission" id="modal-new-permission" tabindex="-1" role="dialog" aria-labelledby="newPermission" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="newPermission">{!! ucfirst(trans('textos.permiso_nuevo')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('permissions.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    
                    <div class="form-group row">
                        {{-- Funcionalidad --}}
                        <label for="functionality" class="col-lg-12">{!! ucfirst(trans('textos.funcionalidad')) !!} *</label>
                        <div class="col-lg-12">
                            @php
                            $options = [];
                            @endphp

                            @foreach($functionalities as $functionality)
                                @php
                                $options[$functionality->slug] = ucfirst(trans('textos.'.$functionality->label));
                                @endphp
                            @endforeach 

                            <x-select name="functionality" class="select2" label="funcionalidad_selec" :options="$options" required info="permission-functionality"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Nombre permiso --}}
                        <label for="name" class="col-lg-12">{!! ucfirst(trans('textos.permiso')) !!} *</label>
                        <div class="col-lg-12">
                            <x-input type="text" name="name" autocomplete="off" maxlength="255" required/>
                        </div>
                    </div>

                    <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                </form>    
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function(){
    //Tabla Permissions:
    if($('#tblPermissions').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        //Filters:
        $('#tblPermissions thead tr').clone(true).appendTo('#tblPermissions thead');
        $('#tblPermissions thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 2){
                $(this).html('');
            }else{
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
            }
     
            $('input', this).on('keyup change', function(){
                if(tablePermissions.column(i).search() !== this.value){
                    tablePermissions
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });
        
        var tablePermissions = $('#tblPermissions').DataTable({
            serverSide: true,
            processing: true, 
            //scrollX: true,
            //fixedColumns: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/permissions",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'permission'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
                {"targets": -1,
                "render": function(data, type, row){
                    //Eliminar:
                    var eliminar = '';
                    if(row['permissions_destroy']){
                        eliminar = '<form method="POST" action="/admin/permissions/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtPermisoConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
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
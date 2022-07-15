{{-- ,
            "columnDefs": [
                {"targets": -1,
                "render": function(data, type, row){
                    //Estado:
                    var checked = (row['status'] == 1)? 'checked':'';
                    var estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtGrupoOnOff+'">';
                        estado += '<input type="checkbox" name="status" '+checked+'><span class="chk" data-id="'+row['id']+'" data-ctrl="stores/status"></span>';
                    estado += '</label>';

                    //Editar:
                    var editar = '<a href="/admin/stores/'+row['id']+'/edit" class="btn btn-sm btn-info btn-module" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';

                    //Eliminar:
                    var eliminar = '<form method="POST" action="stores/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                        eliminar += '<input name="_method" type="hidden" value="DELETE">';
                        eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                        eliminar += '<button type="submit" class="btn btn-sm btn-danger frm-confirm" rel="tooltip" title="'+txtAlmacenEliminar+'" data-msg="'+txtAlmacenConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                    eliminar += '</form>';

                    return estado+' '+editar+' '+eliminar;
                }
                }
            ] --}}
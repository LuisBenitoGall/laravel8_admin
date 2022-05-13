/* DATATABLES: */

//Enlace para widget de traducciones:
function linkTranslations(objid, schema, title){
    return '<a href="#" class="btn btn-xs btn-info btn-translations" rel="tooltip" title="'+title+'" data-toggle="modal" data-target="#modal-translations" data-id="'+objid+'" data-schema="'+schema+'"><i class="fa fa-globe"></i></a>';
}

//Convertir segundos a minutos y horas:
function secondsToString(seconds, viewSeconds = false){
    var hour = Math.floor(seconds / 3600);
    hour = (hour < 10)? '0' + hour : hour;
    var minute = Math.floor((seconds / 60) % 60);
    minute = (minute < 10)? '0' + minute : minute;
    if(viewSeconds){
        var second = seconds % 60;
        second = (second < 10)? '0' + second : second;
        return hour + ':' + minute + ':' + second;
    }else{
        return hour + ':' + minute;    
    }
}

//Generar avatar:
function setAvatar(str1, str2 = false){
    var arr = {
        'A':'11E',
        'B':'22D',
        'C':'33C',
        'D':'44B',
        'E':'55A',
        'F':'669',
        'G':'EE9',
        'H':'DD8',
        'I':'CC7',
        'J':'BB6',
        'K':'AA5',
        'L':'994',
        'M':'FF1',
        'N':'1FF',
        'O':'449',
        'P':'55A',
        'Q':'66B',
        'R':'77C',
        'S':'88D',
        'T':'99E',
        'U':'966',
        'V':'AA5',
        'W':'BB4',
        'X':'CC3',
        'Y':'DD2',
        'Z':'EE1'
    };

    var l1 = str1.charAt(0).toUpperCase();
    var l2 = str2? str2.charAt(0).toUpperCase():false;
    var bg = '#'+arr[l1]+arr[l2];

    return '<span class="avatar mr-2" style="background-color: '+bg+'">'+l1+' '+l2+'</span>';
}

//Calcular importe:
function setAmount(qty, price, discount = false, days = false, iva = false){
    if(qty > 0 && price > 0){
        var amount = qty*price;

        //Jornadas:
        if(days > 1){
            amount = amount * days;

            //Aplicando descuento de días continuos:
            //19/01/22: PENDIENTE
        }

        //Descuento:
        if(discount > 0){
            var percent = (amount * discount) / 100;
            amount = parseFloat(amount) - parseFloat(percent);
        }

        //IVA:
        if(iva > 0){
            var percent = (amount * iva) / 100;
            amount = parseFloat(amount) + parseFloat(percent);
        }

        return amount.toFixed(2);
    }else{
        return false;
    }
}

//Multiple status:
function multipleStatus(data, condition, id){
    var html = '<div class="mini-modal"><a href="#" class="btn btn-xs btn-info btnMiniModal" rel="tooltip" title="'+txtEstado+'"><i class="fa fa-filter"></i></a>';
        html += '<div class="mini-modal-content"><div class="mini-modal-items">';
            html += '<span class="arrow"></span>';
            var arr = data.split(";");
            for(j=0; j<arr.length; j++){
                var p = arr[j].split('|');
                var selected = (condition == p[0])? 'selected':'';
                if(p[1]){
                    html += '<p class="mm-option '+selected+'" data-id="'+id+'" data-option="'+p[0]+'">'+p[1]+'</p>';
                }
            }
        html += '</div></div>';
    html += '</div>';  

    return html;  
}

$(document).ready(function(){
    //Configuración general datatables:
    $.extend(true, $.fn.dataTable.defaults, {
        dom: "Blfrtip",
        buttons: [
            'excel', 'print', 'pdf'
        ],
        "language" : {
            "url": "/js/plugins/datatables/lang/"+idm+".json"
        },
        stateSave: false,    //Guarda página al refrescar. 
        initComplete: function(){
            //Búsqueda automática al clicar sobre el td.filtro:
            var api = this.api();
            api.$('td.filtro').click(function(){
                api.search(this.innerHTML).draw();
            });
        }
    });

    $('.dTable').DataTable({
        stateSave: true,    //Guarda página al refrescar. 
    });

    //Tabla Accounts:
    if($('#tblAccounts').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblAccounts').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/accounts",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name'},
                {data: 'rate', className: 'text-right'},
                {data: 'duration', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": -1,
                "render": function(data, type, row){
                    var estado = '';
                    var editar = '';
                    if(row['editar']){
                        //Estado:
                        var checked = (row['status'] == 1)? 'checked':'';
                        estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtCuentaOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+'><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="accounts/status"></span>';
                        estado += '</label>';

                        //Editar:
                        editar = '<a href="/admin/accounts/'+row['id']+'/edit" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['editar']){
                        eliminar = '<form method="POST" action="accounts/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtCuentaEliminar+'" data-msg="'+txtCuentaConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Batch Patterns:
    if($('#tblBatchPatterns').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblBatchPatterns').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/batch-patterns",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name', className: 'capitalize'},
                {data: 'patron'},
                {data: 'id', className: 'text-center'},
                {data: 'alta', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": 2,
                "render": function(data, type, row){
                    if(row['yearly_reset'] == 1){
                        return '<i class="fa fa-check text-success"></i>';
                    }else{
                        return '<i class="fa fa-ban text-danger"></i>';
                    }
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    var estado = '';
                    var editar = '';
                    if(row['editar']){
                        //Estado:
                        var checked = (row['status'] == 1)? 'checked':'';
                        var estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtPatronOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+'><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="batch-patterns/status"></span>';
                        estado += '</label>';

                        //Editar:
                        editar = '<a href="batch-patterns/'+row['id']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['eliminar']){
                        eliminar = '<form method="POST" action="batch-patterns/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtPatronLotesConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Budget Form Blocks:
    if($('#tblBudgetFormBlocks').length > 0){
        var form = $('#formId').val();
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        //Filters:
        $('#tblBudgetFormBlocks thead tr').clone(true).appendTo('#tblBudgetFormBlocks thead');
        $('#tblBudgetFormBlocks thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 1){
                $(this).html('');
            }else{
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
            }
     
            $('input', this).on('keyup change', function(){
                if(tableBudgetFormBlocks.column(i).search() !== this.value){
                    tableBudgetFormBlocks
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });
        
        var tableBudgetFormBlocks = $('#tblBudgetFormBlocks').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/budget-form-items/"+form,
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": -1,
                "render": function(data, type, row){
                    var trans = '';
                    var editar = '';
                    var eliminar = '';
                    if(row['editar']){
                        //Translations:
                        trans = linkTranslations(row['id'], 'budget_form_items', txtTraducciones);

                        //Editar:
                        editar = '<a href="#" class="btn btn-xs btn-info btn-module btnEditFormBlock" rel="tooltip" title="'+txtEditar+'" data-toggle="modal" data-target="#modal-edit-block" data-name="'+row['name']+'" data-id="'+data+'"><i class="fa fa-edit"></i></a>';

                        //Eliminar:
                        eliminar = '<form method="POST" action="/admin/budget-form-items/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtBloqueConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return trans+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Budget Form Customers:
    if($('#tblBudgetFormCustomers').length > 0){
        var form = $('#formId').val();
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        //Filters:
        $('#tblBudgetFormCustomers thead tr').clone(true).appendTo('#tblBudgetFormCustomers thead');
        $('#tblBudgetFormCustomers thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 1){
                $(this).html('');
            }else{
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
            }
     
            $('input', this).on('keyup change', function(){
                if(tableBudgetFormCustomers.column(i).search() !== this.value){
                    tableBudgetFormCustomers
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });

        var tableBudgetFormCustomers = $('#tblBudgetFormCustomers').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/budget-form-customers/"+form,
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": -1,
                "render": function(data, type, row){
                    //Formulario vinculado a cliente:
                    var vinculado = '';
                    if(row['editar']){
                        var checked = (row['budget_form_id'] == 1)? 'checked':'';
                        vinculado = '<label class="custom-chk-rdb btnSetCustomerForm" rel="tooltip" title="'+txtFormAsociarCliente+'">';
                            vinculado += '<input type="checkbox" name="budget_form_id" '+checked+'><span class="chk chk-link" data-id="'+row['id']+'" data-form="'+form+'"></span>';
                        vinculado += '</label>';
                    }

                    return vinculado;
                }
                }
            ]
        });
    }

    //Tabla Budget Form Items:
    if($('#tblBudgetFormItems').length > 0){
        var form = $('#formId').val();
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        //Filters:
        $('#tblBudgetFormItems thead tr').clone(true).appendTo('#tblBudgetFormItems thead');
        $('#tblBudgetFormItems thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 4){
                $(this).html('');
            }else{
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
            }
     
            $('input', this).on('keyup change', function(){
                if(tableBudgetFormItems.column(i).search() !== this.value){
                    tableBudgetFormItems
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });
        
        var tableBudgetFormItems = $('#tblBudgetFormItems').DataTable({
            "serverSide": true,
            /*"processing": true, */
            //scrollX: true,
            //fixedColumns: true,
            "ajax": {
                "type": "GET",
                "url": "/api/admin/budget-form-items/"+form+"/1",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name'},
                {data: 'Bloque'},
                {data: 'price', className: 'text-right'},
                {data: 'discount', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {
                    "targets": 0,
                    "width": "50%"
                },
                {"targets": 0,
                "render": function(data, type, row){
                    var name = row['name']? row['name']:row['productName'];
                    return name;
                }
                },
                {"targets": 2,
                "render": function(data, type, row){
                    if(row['calculated_by_percent'] && row['calculated_by_percent'] > 0){
                        return txtPorPorcentaje+' ('+row['calculated_by_percent']+'%)';
                    }else{
                        return data+'€';
                    }
                }
                },
                {"targets": 3,
                "render": function(data, type, row){
                    if(data){
                        return data+'%';
                    }else{
                        return '0%';
                    }
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    var fixed = '';
                    var trans = '';
                    var editar = '';
                    var eliminar = '';
                    if(row['editar']){
                        //Fijo en los presupuestos:
                        var checked = (row['fixed_in_budget'] == 1)? 'checked':'';
                        fixed = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtItemFijoEnPresupuesto+'">';
                            fixed += '<input type="checkbox" name="status" '+checked+' ><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="/admin/budget-form-items/fixed"></span>';
                        fixed += '</label>';

                        //Translations:
                        trans = linkTranslations(row['id'], 'budget_form_items', txtTraducciones);

                        var name = row['name']? row['name']:row['productName'];

                        //Editar:
                        editar = '<a href="#" class="btn btn-xs btn-info btn-module btnEditFormItem" rel="tooltip" title="'+txtEditar+'" data-toggle="modal" data-target="#modal-edit-item" data-name="'+name+'" data-id="'+data+'" data-observations="'+row['observations']+'" data-price="'+row['price']+'" data-discount="'+row['discount']+'" data-qtymin="'+row['qty_min']+'" data-calculatebypercent="'+row['calculated_by_percent']+'"><i class="fa fa-edit"></i></a>';

                        //Eliminar:
                        eliminar = '<form method="POST" action="/admin/budget-form-items/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtItemConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return fixed+' '+trans+' '+editar+' '+eliminar;
                }
                }
            ]

        });
    }

    //Tabla Budget Forms:
    if($('#tblBudgetForms').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblBudgetForms').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/budget-forms",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name'},
                {data: 'alta', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": -1,
                "render": function(data, type, row){
                    //Editar:
                    var estado = '';
                    var editar = '';
                    if(row['editar']){
                        //Estado:
                        var checked = (row['status'] == 1)? 'checked':'';
                        var estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtFormOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+'><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="budget-forms/status"></span>';
                        estado += '</label>';

                        editar = '<a href="/admin/budget-forms/'+row['id']+'/edit" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['eliminar']){
                        eliminar = '<form method="POST" action="budget-forms/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtFormConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Budget Items:
    if($('#tblBudgetItems').length > 0){
        function format(d){
            // `d` is the original data object for the row
            return '<table cellpadding="0" cellspacing="0" border="0">'+
                '<tr>'+
                    '<td colspan="8" class="no-border">'+d.observations+'</td>'+
                '</tr>'+
            '</table>';
        }

        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var shared = $('#myShared').val();

        var tableBudgetItems = $('#tblBudgetItems').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            drawCallback: function() {
                $('.select2-dt').select2();
            },
            "ajax": {
                "type": "GET",
                "url": "/api/admin/order-items/"+shared,
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'id', className: 'text-right'},
                {data: 'concept', className: 'capitalize'},
                {data: 'qty', className: 'text-right'},
                {data: 'price', className: 'text-right'},
                {data: 'days', className: 'text-right'},
                {data: 'discount', className: 'text-right'},
                {data: 'amount', className: 'text-right'},
                {data: 'id', className: 'text-right'}
                // {className: 'dt-control', "orderable": false, "data": null,
                // "defaultContent": ''}
            ],
            "columnDefs": [
                {"targets": 0,
                "render": function(data, type, row, meta){
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
                },
                {"targets": 3,
                "render": function(data, type, row){
                    return row['price']+row['currency'];
                }
                },
                {"targets": 4,
                "render": function(data, type, row){
                    if(row['days'] == 0){
                        return '';
                    }else{
                        return row['days'];    
                    }                    
                }
                },
                {"targets": 5,
                "render": function(data, type, row){
                    if(row['discount'] > 0){
                        return row['discount']+'%';
                    }else{
                        return '';    
                    }                    
                }
                },
                {"targets": 6,
                "render": function(data, type, row){
                    var amount = setAmount(row['qty'], row['price'], row['discount'], row['days']);
                    if(amount){
                        return amount+row['currency'];
                    }else{
                        return '0'+row['currency'];
                    }
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    //Ver:
                    var ver = '<a href="#" class="btn btn-xs btn-info btnSeeItem" rel="tooltip" title="'+txtVer+'"><i class="fa fa-eye"></i></a>';

                    //Editar:
                    var editar = '';
                    if(row['editar']){
                        //Editar:
                        editar = '<a href="#" class="btn btn-xs btn-info btnEditItem" rel="tooltip" title="'+txtEditar+'" data-toggle="modal" data-target="#modal-edit-line" data-id="'+row['id']+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['editar']){
                        eliminar = '<form method="POST" action="/admin/items/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<input type="hidden" name="environment" value="budgets">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtItemEliminar+'" data-msg="'+txtPresupuestoLineaConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return ver+' '+editar+' '+eliminar;
                }
                }
            ],
            "footerCallback": function(row, data, start, end, display){
                //console.log(data);
                var api = this.api(), data;

                var currency = false;
                api.rows().every(function(){
                    var data = this.data();
                    if(!currency){
                        currency = data.currency;
                    }
                });

                if(!currency) currency = '€';

                //Separamos el importe de la moneda:
                var intVal = function(i){
                    return typeof i === 'string'? 
                    i.replace(/[\'+currency+',]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
                };

                //Total por filtro aplicado:
                amount = api
                .column(6)
                .data()
                .reduce(function(a, b){
                    return intVal(a) + intVal(b);
                }, 0);

                $('#totalAmountIva').text(amount.toFixed(2)+currency);
            }
        });

        $('#tblBudgetItems tbody').on('click', 'td .btnSeeItem', function () {
            var tr = $(this).closest('tr');
            var row = tableBudgetItems.row(tr);
     
            if(row.child.isShown()){
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            }else{
                // Open this row
                row.child(format(row.data())).show();
                tr.addClass('shown');
            }
        });
    }

    //Tabla Budget Patterns:
    if($('#tblBudgetPatterns').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblBudgetPatterns').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/budget-patterns",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name', className: 'capitalize'},
                {data: 'patron'},
                {data: 'id', className: 'text-center'},
                {data: 'alta', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": 2,
                "render": function(data, type, row){
                    if(row['yearly_reset'] == 1){
                        return '<i class="fa fa-check text-success"></i>';
                    }else{
                        return '<i class="fa fa-ban text-danger"></i>';
                    }
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    var estado = '';
                    var editar = '';
                    if(row['editar']){
                        //Estado:
                        var checked = (row['status'] == 1)? 'checked':'';
                        var estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtPatronOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+'><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="budget-patterns/status"></span>';
                        estado += '</label>';

                        //Editar:
                        editar = '<a href="budget-patterns/'+row['id']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['eliminar']){
                        eliminar = '<form method="POST" action="budget-patterns/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtPatronPresupuestoConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Budget Settings:
    if($('#tblBudgetSettings').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        //Filters:
        $('#tblBudgetSettings thead tr').clone(true).appendTo('#tblBudgetSettings thead');
        $('#tblBudgetSettings thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
     
            $('input', this).on('keyup change', function(){
                if(tableBudgetSettings.column(i).search() !== this.value){
                    tableBudgetSettings
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });
        
        var tableBudgetSettings = $('#tblBudgetSettings').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/budget-settings",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name', className: 'capitalize'},
                {data: 'alta', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": -1,
                "render": function(data, type, row){
                    var estado = '';
                    var editar = '';
                    var principal = '';
                    if(row['editar']){
                        var checked = (row['status'] == 1)? 'checked':'';
                        estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtPlantillaOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+' ><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="budget-settings/status"></span>';
                        estado += '</label>';

                        //Editar:
                        editar = '<a href="budget-settings/'+row['id']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Plantilla principal:
                    if(row['featured'] == 1){
                        principal = '<i class="fa fa-star text-warning ml-1 mr-1" rel="tooltip" title="'+txtPlantillaPredefinida+'"></i>';
                    }else if(row['featured'] == 0 && row['status'] == 1 && row['editar']){
                        principal = '<a href="budget-settings/'+row['id']+'/featured" class="btn btn-xs btn-info" rel="tooltip" title="'+txtPlantillaPredefinir+'"><i class="fa fa-star"></i></a>';    
                    }  

                    //Eliminar:
                    var eliminar = '';
                    if(row['eliminar']){
                        eliminar = '<form method="POST" action="budget-settings/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtPlantillaConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+editar+' '+principal+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Budgets:
    if($('#tblBudgets').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var from = $('#dtFrom').val();

        //Filters:
        $('#tblBudgets thead tr').clone(true).appendTo('#tblBudgets thead');
        $('#tblBudgets thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 10){
                $(this).html('');
            }else{
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
            }
     
            $('input', this).on('keyup change', function(){
                if(tableBudgets.column(i).search() !== this.value){
                    tableBudgets
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });

        var tableBudgets = $('#tblBudgets').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            drawCallback: function() {
                $('.select2-dt').select2();
            },
            "ajax": {
                "type": "GET",
                "url": "/api/admin/budgets/"+from,
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'num', className: 'text-right'},
                {data: 'budget_old_num', className: 'text-right'},
                {data: 'name'},
                {data: 'id'},
                {data: 'id', className: 'text-right'},
                {data: 'modalidad'},
                {data: 'id'},
                {data: 'id', className: 'text-right'},
                {data: 'Company'},
                {data: 'estado'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": 0,
                "render": function(data, type, row){
                    var num = row['budget_num']? row['budget_num']:row['budgetId'];
                    if(row['from'] == 'customers'){
                        var nbudget = '<a href="/admin/budgets/'+row['budgetId']+'/edit">'+num+'</a>';
                    }else{
                        var nbudget = '<a href="/admin/budgets/'+row['budgetId']+'/show">'+num+'</a>';    
                    }                    
                    return nbudget;
                }
                },
                {"targets": 3,
                "render": function(data, type, row){
                    if(!row['order_num'] && !row['orderId']){
                        return '<span class="text-warning">'+txtPendiente+'</span>';   
                    }else{
                        var order_num = row['order_num']? row['order_num']:row['orderId'];
                        return '<a target="_blank" href="/admin/orders/'+row['orderId']+'/edit">'+order_num+'</a>';
                    }
                }
                },
                {"targets": 4,
                "render": function(data, type, row){
                    return row['amount'];
                }
                },
                {"targets": 6,
                "render": function(data, type, row){
                    if(row['Nombre']){
                        return row['Nombre']+' '+row['surname'];
                    }else{
                        return '';
                    }
                }
                },
                {"targets": 7,
                "render": function(data, type, row){
                    return '<span style="display: none">'+row['created_at']+'</span>'+row['alta'];
                }
                },
                {"targets": 8,
                "render": function(data, type, row){
                    var company = row['Company'];
                    if(row['CustomerManagerName']){
                        company += ' ('+row['CustomerManagerName']+' '+row['CustomerManagerSurname']+')';
                    }

                    return company;
                }
                },
                {"targets": -2,
                "render": function(data, type, row){
                    return '<span class="xoption-'+row['budgetId']+'">'+row['estado']+'</span>'; 
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    //Estado múltiple:
                    // var estado = '<div class="mini-modal"><a href="#" class="btn btn-xs btn-info btnMiniModal" rel="tooltip" title="'+txtEstado+'"><i class="fa fa-filter"></i></a>';
                    //     estado += '<div class="mini-modal-content"><div class="mini-modal-items">';
                    //         estado += '<span class="arrow"></span>';
                    //         var arr = row['estados'].split(";");
                    //         for(j=0; j<arr.length; j++){
                    //             var p = arr[j].split('|');
                    //             var selected = (row['status'] == p[0])? 'selected':'';
                    //             if(p[1]){
                    //                 estado += '<p class="mm-option '+selected+'" data-id="'+row['budgetId']+'" data-option="'+p[0]+'">'+p[1]+'</p>';
                    //             }
                    //         }
                    //     estado += '</div></div>';
                    // estado += '</div>';

                    var estado = multipleStatus(row['estados'], row['status'], row['budgetId']);

                    //Editar:
                    var editar = '';
                    if(row['editar']){
                        if(row['from'] == 'customers'){
                            var action = 'edit';   
                            var icon = 'edit'; 
                            var txt_ = txtEditar;
                        }else{
                            var action = 'show';
                            var icon = 'eye';
                            var txt_ = txtVer;
                        }                        
                        var editar = '<a href="/admin/budgets/'+row['budgetId']+'/'+action+'" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txt_+'"><i class="fa fa-'+icon+'"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['from'] == 'customers' && row['eliminar']){
                        var eliminar = '<form method="POST" action="/admin/budgets/'+row['budgetId']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtPresupuestoConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Business Areas:
    if($('#tblBusinessAreas').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var company_id = $('#companyId').val();
        var business_relationship = $('#businessRelationship').val();
        $('#companyId').remove();
        
        $('#tblBusinessAreas').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/business-areas/"+company_id+"/"+business_relationship,
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": -1,
                "render": function(data, type, row){
                    var estado = '';
                    var costes = '';
                    var editar = '';
                    if(row['editar']){
                        //Estado:
                        var checked = (row['status'] == 1)? 'checked':'';
                        estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtEstado+'">';
                            estado += '<input type="checkbox" name="status" '+checked+' ><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="/admin/business-areas/status"></span>';
                        estado += '</label>';

                        //Costes del área de negocio:
                        costes = '<a href="/admin/business-areas/'+row['id']+'/costs" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtCostesHistorico+'"><i class="fa fa-money-bill-alt"></i></a>';

                        //Editar:
                        editar = '<a href="/admin/business-areas/'+row['id']+'/edit/'+row['br']+'" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['eliminar']){
                        eliminar = '<form method="POST" action="/admin/business-areas/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<input type="hidden" name="business_relationship" value="'+row['br']+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtAreaNegocioEliminar+'" data-msg="'+txtAreaNegocioConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+costes+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Companies:
    if($('#tblCompanies').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        //Filters:
        $('#tblCompanies thead tr').clone(true).appendTo('#tblCompanies thead');
        $('#tblCompanies thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 4 || i == 5){
                $(this).html('');
            }else{
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
            }
     
            $('input', this).on('keyup change', function(){
                if(tableCompanies.column(i).search() !== this.value){
                    tableCompanies
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });

        var tableCompanies = $('#tblCompanies').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/companies",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name'},
                {data: 'tradename'},
                {data: 'id', className: 'text-right'},
                {data: 'nif'},
                {data: 'id', className: 'text-center'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": 2,
                "render": function(data, type, row){
                    return '<span style="display: none">'+row['created_at']+'</span>'+row['alta'];
                }
                },
                {"targets": 4,
                "render": function(data, type, row){
                    if(row['logo']){
                        var logo = '<img src="/img/companies/'+row['logo']+'" width="50">';
                    }else{
                        var logo = '<i class="fa fa-building icono"></i>';
                    }   
                    return logo;
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    var estado = '';
                    var transfer = '';
                    var editar = '';
                    if(row['editar']){
                        //Estado:
                        var checked = (row['status'] == 1)? 'checked':'';
                        estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtEmpresaOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+'><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="companies/status"></span>';
                        estado += '</label>';

                        //Traspaso de empresa:
                        transfer = '<a href="#" class="btn btn-xs btn-info btn-module btnTransferCompany" rel="tooltip" title="'+txtEmpresaTraspaso+'" data-toggle="modal" data-target="#modal-transfer-company" data-id="'+row['id']+'"><i class="fa fa-handshake"></i></a>';

                        //Editar:
                        editar = '<a href="/admin/companies/'+row['id']+'/edit" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['eliminar']){
                        eliminar = '<form method="POST" action="companies/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEmpresaEliminar+'" data-msg="'+txtEmpresaConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+transfer+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Company Accounts:
    if($('#tblCompanyAccounts').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblCompanyAccounts').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/company-accounts",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name'},
                {data: 'inicio', className: 'text-right'},
                {data: 'fin', className: 'text-right'},
                {data: 'price', className: 'text-right'},
                {data: 'status', className: 'text-center',},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
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

    //Tabla Contents:
    if($('#tblContents').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        //Filters:
        $('#tblContents thead tr').clone(true).appendTo('#tblContents thead');
        $('#tblContents thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 2){
                $(this).html('');
            }else{
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
            }
     
            $('input', this).on('keyup change', function(){
                if(tableContents.column(i).search() !== this.value){
                    tableContents
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });
        
        var tableContents = $('#tblContents').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/contents",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name'},
                {data: 'code'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": -1,
                "render": function(data, type, row){
                    var estado = '';
                    var editar = '';
                    if(row['editar']){
                        //Estado:
                        var checked = (row['status'] == 1)? 'checked':'';
                        estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtEstado+'">';
                            estado += '<input type="checkbox" name="status" '+checked+' ><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="contents/status"></span>';
                        estado += '</label>';

                        //Editar:
                        editar = '<a href="/admin/contents/'+row['id']+'/edit" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['eliminar']){
                        eliminar = '<form method="POST" action="contents/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtContenidoEliminar+'" data-msg="'+txtContenidoConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Contracts:
    if($('#tblContracts').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblContracts').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/contracts",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name'},
                {data: 'alta', className: 'text-right'},
                {data: 'duration', className: 'text-right'},
                {data: 'with_holidays', className: 'text-center'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": 3,
                "render": function(data, type, row){
                    if(row['with_holidays']){
                        return '<i class="fa fa-check text-success" rel="tooltip" title="'+txtIncluyeVacaciones+'"></i>';
                    }else{
                        return '<i class="fa fa-times text-danger" rel="tooltip" title="'+txtSinVacaciones+'"></i>';
                    }
                }
                }, 
                {"targets": -1,
                "render": function(data, type, row){
                    var estado = '';
                    var editar = '';
                    if(row['editar']){
                        //Estado:
                        var checked = (row['status'] == 1)? 'checked':'';
                        estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtEstado+'">';
                            estado += '<input type="checkbox" name="status" '+checked+' ><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="contracts/status"></span>';
                        estado += '</label>';

                        //Editar:
                        editar = '<a href="/admin/contracts/'+row['id']+'/edit" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['eliminar']){
                        eliminar = '<form method="POST" action="contracts/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtContratoEliminar+'" data-msg="'+txtContratoConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Cost Centers:
    if($('#tblCostCenters').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var company_id = $('#companyId').val();
        var business_relationship = $('#businessRelationship').val();
        $('#companyId').remove();
        
        $('#tblCostCenters').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/cost-centers/"+company_id+"/"+business_relationship,
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": -1,
                "render": function(data, type, row){
                    var estado = '';
                    var costes = '';
                    var editar = '';
                    if(row['editar']){
                        //Estado:
                        var checked = (row['status'] == 1)? 'checked':'';
                        estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtEstado+'">';
                            estado += '<input type="checkbox" name="status" '+checked+' ><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="/admin/cost-centers/status"></span>';
                        estado += '</label>';

                        //Costes del centro:
                        costes = '<a href="/admin/cost-centers/'+row['id']+'/costs" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtCostesHistorico+'"><i class="fa fa-money-bill-alt"></i></a>';

                        //Editar:
                        editar = '<a href="/admin/cost-centers/'+row['id']+'/edit/'+row['br']+'" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['eliminar']){
                        eliminar = '<form method="POST" action="/admin/cost-centers/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<input type="hidden" name="business_relationship" value="'+row['br']+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtCentroCosteEliminar+'" data-msg="'+txtCentroCosteConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+costes+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Countries:
    if($('#tblCountries').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        $('#tblCountries').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/countries",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name'},
                {data: 'code'},
                {data: 'alfa2'},
                {data: 'alfa3'},
                {data: 'flag'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": 0,
                "render": function(data, type, row){
                    return row['name'].charAt(0).toUpperCase()+row['name'].slice(1);
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    var estado = '';
                    var provinces = '';
                    var editar = '';
                    if(row['editar']){
                        //Estado:
                        var checked = (row['status'] == 1)? 'checked':'';
                        estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtEstado+'">';
                            estado += '<input type="checkbox" name="status" '+checked+' ><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="countries/status"></span>';
                        estado += '</label>';

                        //Provincias:
                        provinces = '<a href="/admin/provinces/'+row['id']+'" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtProvincias+'"><i class="fa fa-flag"></i></a>';

                        //Editar:
                        editar = '<a href="#" class="btn btn-xs btn-info btn-module btnEditCountry" rel="tooltip" title="'+txtEditar+'" data-toggle="modal" data-target="#modal-edit-country" data-id="'+row['id']+'" data-country="'+row['name']+'" data-code="'+row['code']+'" data-alfa2="'+row['alfa2']+'" data-alfa3="'+row['alfa3']+'" data-flag="'+row['flag']+'" data-status="'+row['status']+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['eliminar']){
                        eliminar = '<form method="POST" action="countries/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtPaisConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+provinces+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Currencies:
    if($('#tblCurrencies').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        //Filters:
        $('#tblCurrencies thead tr').clone(true).appendTo('#tblCurrencies thead');
        $('#tblCurrencies thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 4){
                $(this).html('');
            }else{
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
            }
     
            $('input', this).on('keyup change', function(){
                if(tableCurrencies.column(i).search() !== this.value){
                    tableCurrencies
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });
        
        var tableCurrencies = $('#tblCurrencies').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/currencies",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name'},
                {data: 'code'},
                {data: 'number', className: 'text-right'},
                {data: 'symbol', className: 'text-center'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": -1,
                "render": function(data, type, row){
                    var estado = '';
                    var editar = '';
                    if(row['editar']){
                        //Estado:
                        var checked = row['status'] == 1? 'checked':'';
                        var activo = row['status'] == 1? 1:'0';
                        estado = '<span style="display: none">'+activo+'</span><label class="custom-chk-rdb" rel="tooltip" title="'+txtOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+' ><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="currencies/status"></span>';
                        estado += '</label>';

                        //Editar:
                        editar = '<a href="#" class="btn btn-xs btn-info btn-module btnEditCurrency" rel="tooltip" title="'+txtEditar+'" data-toggle="modal" data-target="#modal-edit-currency" data-id="'+row['id']+'" data-currency="'+row['name']+'" data-symbol="'+row['symbol']+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['eliminar']){
                        eliminar = '<form method="POST" action="currencies/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtMonedaEliminar+'" data-msg="'+txtMonedaConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Customer Products:
    if($('#tblCustomerProducts').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var customer = $('#dtCustomer').val();
        var ba = $('#dtBusinessArea').val();

        //Filters:
        $('#tblCustomerProducts thead tr').clone(true).appendTo('#tblCustomerProducts thead');
        $('#tblCustomerProducts thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 4){
                $(this).html('');
            }else{
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
            }
     
            $('input', this).on('keyup change', function(){
                if(tableCustomerProducts.column(i).search() !== this.value){
                    tableCustomerProducts
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });
        
        var tableCustomerProducts = $('#tblCustomerProducts').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/customer-products/"+customer+"/"+ba,
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'producto'},
                {data: 'descripcion'},
                {data: 'id', className: 'text-center'},
                {data: 'price', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": 2,
                "render": function(data, type, row){
                    if(row['doc']){
                        return '<img src="/img/products/'+row['doc']+'" width="60">';      
                    }else{
                        return '<i class="fa fa-gift icono"></i>';    
                    }                   
                }
                }, 
                {"targets": 3,
                "render": function(data, type, row){
                    var readonly = row['active'] == 0? 'readonly':'';
                    var price = '<div class="input-group">';
                        price += '<input type="text" class="form-control text-right customerProductPrice" data-customer="'+customer+'" data-product="'+row['id']+'" value="'+row['price']+'" '+readonly+'>';
                        price += '<div class="input-group-append"><span class="input-group-addon">'+row['currency']+'</span></div>';
                    price += '</div>'; 
                    return price;   
                }
                }, 
                {"targets": -1,
                "render": function(data, type, row){
                    //Estado:
                    var checked = row['active'] == 1? 'checked':'';
                    var estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtProductoOnOff+'">';
                        estado += '<input type="checkbox" name="active" '+checked+'><span class="chk btnCustomerProductStatus" data-product="'+row['id']+'" data-price="'+row['price']+'"></span>';
                    estado += '</label>';

                    return estado;
                }
                } 
            ] 
        });
    }

    //Tabla Customers:
    if($('#tblCustomers').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        //Filters:
        $('#tblCustomers thead tr').clone(true).appendTo('#tblCustomers thead');
        $('#tblCustomers thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 3){
                $(this).html('');
            }else{
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');        
            }
     
            $('input', this).on('keyup change', function(){
                if(tableCustomers.column(i).search() !== this.value){
                    tableCustomers
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });
        
        var tableCustomers = $('#tblCustomers').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/customers",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name'},
                {data: 'tradename'},
                {data: 'nif'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": -1,
                "render": function(data, type, row){
                    //Estado:
                    var estado = '';
                    //Responsables:
                    var users = '';
                    //Configuraciones:
                    var config = '';
                    if(row['status_permission']){
                        var checked = (row['status'] == 1)? 'checked':'';
                        estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+' ><span class="chk chk-action" data-id="'+row['cp_id']+'" data-ctrl="customers/status"></span>';
                        estado += '</label>';

                        users = '<a href="#" class="btn btn-xs btn-info btn-module btnAddManager" rel="tooltip" title="'+txtResponsableAdd+'" data-toggle="modal" data-target="#modal-add-manager" data-id="'+row['id']+'" data-company="'+row['name']+'"><i class="fa fa-user-plus"></i></a>';

                        config = '<a href="#" class="btn btn-xs btn-info btn-module btnCustomerConfig" rel="tooltip" title="'+txtClienteConfiguraciones+'" data-toggle="modal" data-target="#modal-edit-config" data-customer="'+row['id']+'" data-baid="'+row['ba_id']+'" data-workplaceid="'+row['workplace_id']+'" data-ccid="'+row['cc_id']+'" data-discount="'+row['discount']+'"><i class="fa fa-cog"></i></a>';
                    }

                    //Editar:
                    var editar = '';
                    if(!row['account'] || row['account']['guardian'] == row['current_company'] || row['edit']){
                        editar = '<a href="/admin/customers/'+row['id']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Ver:
                    var ver = '<a href="/admin/customers/'+row['cp_id']+'/show" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtVer+'"><i class="fa fa-eye"></i></a>';

                    //Nueva venta + Catálogo:
                    var venta = '';
                    var catalogo = '';
                    if(row['catalogue']){
                        venta = '<a href="/admin/orders/'+row['id']+'/sale" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtVentaNueva+'"><i class="fa fa-cart-plus"></i></a>';

                        catalogo = '<a href="/admin/customer-products/'+row['id']+'" class="btn btn-xs btn-info" rel="tooltip" title="'+txtCatalogo+'"><i class="fa fa-gift"></i></a>';
                    }

                    //Condiciones de pago:
                    var conditions = '';
                    if(row['conditions']){
                        conditions = '<a href="/admin/customers/'+row['id']+'/pay-conditions" class="btn btn-xs btn-info" rel="tooltip" title="'+txtCondicionesPago+'"><i class="fa fa-money-check-alt"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['delete_permission']){
                        eliminar = '<form method="POST" action="customers/'+row['cp_id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<input type="hidden" name="tipo" value="customer">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtClienteEliminar+'" data-msg="'+txtClienteConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+editar+' '+venta+' '+catalogo+' '+conditions+' '+ver+' '+users+' '+config+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Day Types:
    if($('#tblDayTypes').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        $('#tblDayTypes').DataTable({
            "serverSide": false,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/day-types",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name'},
                {data: 'color'},
                {data: 'is_working', className: 'text-center'},
                {data: 'user_enabled', className: 'text-center'},
                {data: 'cumulative', className: 'text-center'},
                {data: 'modifiable', className: 'text-center'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": 1,
                "render": function(data, type, row){
                    return '<span class="colorEx mr-2" style="background-color: '+row['color']+'"></span> '+row['color'];
                }
                },
                {"targets": 2,
                "render": function(data, type, row){
                    if(row['is_working'] == 1){
                        return '<i class="fa fa-check text-success" rel="tooltip" title="'+txtCompatibleLaborable+'"></i>';
                    }else{
                        return '<i class="fa fa-times text-danger" rel="tooltip" title="'+txtCompatibleLaborableNo+'"></i>';
                    }
                }
                },
                {"targets": 3,
                "render": function(data, type, row){
                    if(row['user_enabled'] == 1){
                        return '<i class="fa fa-check text-success" rel="tooltip" title="'+txtDisponibleUsuario+'"></i>';
                    }else{
                        return '<i class="fa fa-times text-danger" rel="tooltip" title="'+txtDisponibleUsuarioNo+'"></i>';
                    }
                }
                },
                {"targets": 4,
                "render": function(data, type, row){
                    if(row['cumulative'] == 1){
                        return '<i class="fa fa-check text-success" rel="tooltip" title="'+txtAcumulable+'"></i>';
                    }else{
                        return '<i class="fa fa-times text-danger" rel="tooltip" title="'+txtAcumulableNo+'"></i>';
                    }
                }
                },
                {"targets": 5,
                "render": function(data, type, row){
                    if(row['modifiable'] == 1){
                        return '<i class="fa fa-check text-success" rel="tooltip" title="'+txtModificable+'"></i>';
                    }else{
                        return '<i class="fa fa-times text-danger" rel="tooltip" title="'+txtModificableNo+'"></i>';
                    }
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    if(row['editar']){
                        //Editar:
                        var editar = '<a href="/admin/day-types/'+row['id']+'/edit" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';

                        //Configuraciones:
                        var config = '<a href="#" class="btn btn-xs btn-info btn-module btnDayTypeConfig" rel="tooltip" title="'+txtConfiguracion+'" data-toggle="modal" data-target="#modal-config-day-types" data-id="'+row['id']+'" data-type="'+row['name']+'"><i class="fa fa-cog"></i></a>';

                        //Eliminar:
                        var eliminar = '<form method="POST" action="/admin/day-types/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtJornadaConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';

                        return editar+' '+config+' '+eliminar;
                    }
                }
                }
            ]
        });
    }

    //Tabla Deliveries:
    if($('#tblDeliveries').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        //Filters:
        $('#tblDeliveries thead tr').clone(true).appendTo('#tblDeliveries thead');
        $('#tblDeliveries thead tr:eq(1) th').each(function(i){
            var title = $(this).text(); 
            if(i == 4){
                $(this).html('');
            }else{
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
                // aquí el código retirado
            }

            $('input', this).on('keyup change', function(){
                if(tableDeliveries.column(i).search() !== this.value){
                    tableDeliveries
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });
        
        var tableDeliveries = $('#tblDeliveries').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/deliveries",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'id', className: 'text-right'},
                {data: 'name'},
                {data: 'alta', className: 'text-right'},
                {data: 'estado'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": 0,
                "render": function(data, type, row){
                    var num = row['id'];
                    if(row['delivery_num']){
                        num = row['delivery_num'];
                    }else if(row['delivery_old_num']){
                        num = row['delivery_old_num'];
                    }

                    var html = '';
                    if(row['editar']){
                        html += '<a href="/admin/deliveries/'+row['id']+'/edit">';
                    }
                    html += num;
                    if(row['editar']){
                        html += '</a>';
                    }
                    return html;
                }
                },
                {"targets": -2,
                "render": function(data, type, row){
                    return '<span class="xoption-'+row['id']+'">'+row['estado']+'</span>'; 
                }
                }, 
                {"targets": -1,
                "render": function(data, type, row){
                    var estado = '';
                    var editar = '';
                    var eliminar = '';
                    if(row['editar']){
                        //Estado múltiple:
                        estado = multipleStatus(row['estados'], row['status'], row['id']);

                        //Editar:
                        editar = '<a href="/admin/deliveries/'+row['id']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';

                        //Eliminar:
                        eliminar = '<form method="POST" action="/admin/deliveries/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtEntregaConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';

                        return estado+' '+editar+' '+eliminar;
                    }
                }
                }
            ]
        });
    }

    //Tabla Deliveries Orders:
    if($('#tblDeliveriesOrders').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        //Filters:
        $('#tblDeliveriesOrders thead tr').clone(true).appendTo('#tblDeliveriesOrders thead');
        $('#tblDeliveriesOrders thead tr:eq(1) th').each(function(i){
            var title = $(this).text(); 
            if(i == 4){
                $(this).html('');
            }else{
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
                // aquí el código retirado
            }

            $('input', this).on('keyup change', function(){
                if(tableDeliveriesOrders.column(i).search() !== this.value){
                    tableDeliveriesOrders
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });

        var tableDeliveriesOrders = $('#tblDeliveriesOrders').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            drawCallback: function() {
                $('.select2-dt').select2();
            },
            "ajax": {
                "type": "GET",
                "url": "/api/admin/deliveries-orders",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'num', className: 'text-right'},
                {data: 'name'},
                {data: 'company'},
                {data: 'id'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": 0,
                "render": function(data, type, row){
                    var num = row['id'];
                    if(row['order_num']){
                        num = row['order_num'];
                    }else if(row['order_old_num']){
                        num = row['order_old_num'];
                    }

                    var html = '';
                    if(row['editar']){
                        html += '<a target="_blank" href="/admin/orders/'+row['id']+'/edit">';
                    }
                    html += num;
                    if(row['editar']){
                        html += '</a>';
                    }
                    return html;
                }
                },
                {"targets": -2,
                "render": function(data, type, row){
                    console.log(row['deliveries']); 
                    var estado = '<select class="form-control select-xs comboEstadoGroup select2-dt">';
                        estado += '<option value="">'+txtEntregaSelec+'</option>';
                        for(j=0; j<row['deliveries'].length; j++){
                            estado += '<option value="" data-id="'+row['deliveries'][j].id+'">'+row['deliveries'][j].id+'</option>';
                        }
                    estado += '</select>';

                    return estado;
                }
                }, 
                {"targets": -1,
                "render": function(data, type, row){
                    var estado = '';
                    var editar = '';
                    var eliminar = '';
                    if(row['editar']){
                        //Estado múltiple:
                        //estado = multipleStatus(row['estados'], row['status'], row['id']);

                        //Editar:
                        editar = '<a target="_blank" href="/admin/orders/'+row['id']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';

                        //Eliminar:
                        eliminar = '<form method="POST" action="/admin/deliveries/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtEntregaConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';

                        return editar+' '+eliminar;
                    }
                }
                }
            ]
        });
    }

    //Tabla Delivery Patterns:
    if($('#tblDeliveryPatterns').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblDeliveryPatterns').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/delivery-patterns",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name', className: 'capitalize'},
                {data: 'patron'},
                {data: 'id', className: 'text-center'},
                {data: 'alta', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": 2,
                "render": function(data, type, row){
                    if(row['yearly_reset'] == 1){
                        return '<i class="fa fa-check text-success"></i>';
                    }else{
                        return '<i class="fa fa-ban text-danger"></i>';
                    }
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    var estado = '';
                    var editar = '';
                    if(row['editar']){
                        //Estado:
                        var checked = (row['status'] == 1)? 'checked':'';
                        var estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtPatronOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+'><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="delivery-patterns/status"></span>';
                        estado += '</label>';

                        //Editar:
                        editar = '<a href="delivery-patterns/'+row['id']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['eliminar']){
                        eliminar = '<form method="POST" action="delivery-patterns/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtPatronPedidoConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Edition Questions:
    if($('#tblEditionQuestions').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var edition_id = $('#editionId').val();
        
        var tableEditionQuestions = $('#tblEditionQuestions').DataTable({
            "order": [[ 0, "asc" ]],
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/quiz-edition-questions/"+edition_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'order', className: 'text-right reorder'},
                {data: 'name'},
                {data: 'tipo'},
                {data: 'tipo_respuesta'},
                {data: 'time', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": 4,
                "render": function(data, type, row){
                    if(row['time']){
                        return row['time'] / 60;
                    }else{
                        return '';
                    }
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    var editar = '';
                    var eliminar = '';
                    if(row['editar']){
                        //Editar:
                        editar = '<a target="_blank" href="/admin/questions/'+row['id']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtCuestionEditar+'"><i class="fa fa-edit"></i></a>';

                        //Eliminar:
                        eliminar = '<form method="POST" action="/admin/quiz-edition-questions/'+row['qeq_id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtEdicionCuestionConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';

                        return editar+' '+eliminar;
                    }
                }
                }
            ],
            "rowReorder": {
                dataSrc: 'order',
                selector: 'tr'
            },
            "responsive": true      
        });
    }

    //Tabla Edition Teams:
    if($('#tblEditionTeams').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var edition_id = $('#editionId').val();
        
        $('#tblEditionTeams').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/quiz-edition-teams/"+edition_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name', className: 'capitalize'},
                {data: 'id', className: 'text-center'},
                {data: 'points', className: 'text-right'},
                {data: 'time', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": 1,
                "render": function(data, type, row){
                    if(row['logo']){
                        var logo = '<img src="/img/teams/'+row['logo']+'" width="50">';
                    }else{
                        var logo = '<i class="fa fa-users icono"></i>';
                    }   
                    return logo;
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    var editar = '';
                    var eliminar = '';
                    if(row['editar']){
                        //Editar:
                        editar = '<a target="_blank" href="/admin/teams/'+row['team_id']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEquipoEditar+'"><i class="fa fa-edit"></i></a>';

                        //Eliminar:
                        eliminar = '<form method="POST" action="/admin/quiz-edition-teams/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtEdicionEquipoConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';

                        return editar+' '+eliminar;
                    }
                }
                }
            ]            
        });
    }

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
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/employees",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'empleado'},
                {data: 'inicio', className: 'text-right'},
                {data: 'fin', className: 'text-right'},
                {data: 'job_title'},
                {data: 'id', className: 'text-center'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
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

    //Tabla GroupMembers:
    if($('#tblGroupMembers').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var group_id = $('#groupId').val();
        
        $('#tblGroupMembers').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/group-members/"+group_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name'},
                {data: 'rol'},
                {data: 'alta'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": 0,
                "render": function(data, type, row){
                    return row['name']+' '+row['surname'];
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    //Estado:
                    var checked = (row['responsible'] == 1)? 'checked':'';
                    var responsible = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtGrupoResponsable+'">';
                        responsible += '<input type="checkbox" name="responsible" '+checked+' ><span class="chk" data-id="'+row['id']+'" data-ctrl="/admin/group_members/responsible"></span>';
                    responsible += '</label>';

                    //Editar:
                    var editar = '<a href="#" data-toggle="modal" data-target="#modal-edit-member" data-id="'+row['id']+'" data-member="'+row['name']+' '+row['surname']+'" data-rol="'+row['rol']+'" class="btn btn-xs btn-info btn-module btnEditMember" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';

                    //Eliminar:
                    var eliminar = '<form method="POST" action="/admin/group_members/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                        eliminar += '<input name="_method" type="hidden" value="DELETE">';
                        eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                        eliminar += '<input type="hidden" name="group_id" value="'+row['group_id']+'">';
                        eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtMiembroConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                    eliminar += '</form>';

                    return responsible+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Groups:
    if($('#tblGroups').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblGroups').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/groups",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name'},
                {data: 'description'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": -1,
                "render": function(data, type, row){
                    //Estado:
                    var checked = (row['status'] == 1)? 'checked':'';
                    var estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtGrupoOnOff+'">';
                        estado += '<input type="checkbox" name="status" '+checked+' ><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="groups/status"></span>';
                    estado += '</label>';

                    //Editar:
                    var editar = '<a href="/admin/groups/'+row['id']+'/edit" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';

                    //Eliminar:
                    var eliminar = '<form method="POST" action="groups/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                        eliminar += '<input name="_method" type="hidden" value="DELETE">';
                        eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                        eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtGrupoConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                    eliminar += '</form>';

                    return estado+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Invoice Patterns:
    if($('#tblInvoicePatterns').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblInvoicePatterns').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/invoice-patterns",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name', className: 'capitalize'},
                {data: 'patron'},
                {data: 'id', className: 'text-center'},
                {data: 'alta', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": 2,
                "render": function(data, type, row){
                    if(row['yearly_reset'] == 1){
                        return '<i class="fa fa-check text-success"></i>';
                    }else{
                        return '<i class="fa fa-ban text-danger"></i>';
                    }
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    var estado = '';
                    var editar = '';
                    if(row['editar']){
                        //Estado:
                        var checked = (row['status'] == 1)? 'checked':'';
                        var estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtPatronOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+'><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="invoice-patterns/status"></span>';
                        estado += '</label>';

                        //Editar:
                        editar = '<a href="invoice-patterns/'+row['id']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['eliminar']){
                        eliminar = '<form method="POST" action="invoice-patterns/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtPatronFacturaConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Invoice Settings:
    if($('#tblInvoiceSettings').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblInvoiceSettings').DataTable({
            "serverSide": false,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/invoice-settings",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name', className: 'capitalize'},
                {data: 'alta', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": -1,
                "render": function(data, type, row){
                    var checked = (row['status'] == 1)? 'checked':'';
                    var estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtPlantillaOnOff+'">';
                        estado += '<input type="checkbox" name="status" '+checked+' ><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="invoice-settings/status"></span>';
                    estado += '</label>';

                    //Editar:
                    var editar = '<a href="invoice-settings/'+row['id']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';

                    //Plantilla predefinida:
                    if(row['featured'] == 1){
                        var predefinida = '<i class="fa fa-star text-warning ml-1 mr-2" rel="tooltip" title="'+txtPlantillaPredefinida+'"></i>';
                    }else{
                        var predefinida = '<a href="invoice-settings/'+row['id']+'/featured" class="btn btn-xs btn-info" rel="tooltip" title="'+txtPlantillaPredefinir+'"><i class="fa fa-star"></i></a>';    
                    }                    

                    //Eliminar:
                    var eliminar = '<form method="POST" action="invoice-settings/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                        eliminar += '<input name="_method" type="hidden" value="DELETE">';
                        eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                        eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtPlantillaConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                    eliminar += '</form>';

                    return estado+' '+editar+' '+predefinida+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla IP Access:
    if($('#tblIpAccess').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblIpAccess').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/ip-access",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'group'},
                {data: 'ip_start'},
                {data: 'ip_end'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": -1,
                "render": function(data, type, row){
                    //Eliminar:
                    var eliminar = '<form method="POST" action="ip-access/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                        eliminar += '<input name="_method" type="hidden" value="DELETE">';
                        eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                        eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtIpRangoConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                    eliminar += '</form>';

                    return eliminar;
                }
                }
            ]
        });
    }

    //Tabla Modules:
    if($('#tblModules').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblModules').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/modules",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'module', className: 'capitalize'},
                {data: 'nivel'},
                {data: 'id', className: 'text-center'},
                {data: 'id', className: 'text-center'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": 2,
                "render": function(data, type, row){
                    var icon = '<i class="fa fa-'+row['icon']+'"></i>';

                    return icon;
                }
                },
                {"targets": 3,
                "render": function(data, type, row){
                    return '<span class="colorEx mr-2" style="background-color: '+row['color']+'"></span>';
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    //Estado, sólo para módulos opcionales:
                    if(row['level'] == 3){
                        var checked = (row['status'] == 1)? 'checked':'';
                        var estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtModuloOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+'><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="modules/status"></span>';
                        estado += '</label>';
                    }else{
                        var estado = '';
                    }

                    //Translations:
                    var trans = linkTranslations(row['id'], 'modules', txtTraducciones);
                    
                    //Editar:
                    var editar = '<a href="modules/'+row['id']+'/edit" class="btn btn-xs btn-info btnModuleEdit" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';

                    //Eliminar:
                    var eliminar = '<form method="POST" action="modules/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                        eliminar += '<input name="_method" type="hidden" value="DELETE">';
                        eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                        eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtModuloConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                    eliminar += '</form>';
                    
                    //var eliminar = '<a href="#" class="btn btn-xs btn-danger btnDelete" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtModuloConfirmEliminar+'" data-model="modules" data-id="'+row['id']+'"><i class="fa fa-edit"></i></a>';

                    // var eliminar = '<a class="btn btn-xs btn-danger" wire:click="$emit("destroy", '+row['id']+')" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtModuloConfirmEliminar+'"><i class="fa fa-trash"></i></a>';

                    return estado+' '+trans+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

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
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/notifications-read",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'id', className: 'capitalize'},
                {data: 'id'},
                {data: 'id', className: 'text-right'},
                {data: 'id', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
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

    //Tabla Notifications Unread:
    if($('#tblNotificationsUnread').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        //Filters:
        $('#tblNotificationsUnread thead tr').clone(true).appendTo('#tblNotificationsUnread thead');
        $('#tblNotificationsUnread thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 3){
                $(this).html('');
            }else{
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
            }
     
            $('input', this).on('keyup change', function(){
                if(tableNotificationsUnread.column(i).search() !== this.value){
                    tableNotificationsUnread
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });
        
        var tableNotificationsUnread = $('#tblNotificationsUnread').DataTable({
            "order": [[ 2, "desc" ]],
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/notifications-unread",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'id', className: 'capitalize'},
                {data: 'id'},
                {data: 'id', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
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
                    return '<span style="display: none">'+row['fiscal_date']+'</span>'+row['alta'];
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
                    var eliminar = '<form method="POST" action="notifications/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
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

    //Tabla Order Customer Team:
    if($('#tblOrderCustomerTeam').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var shared = $('#myShared').val();

        var tableOrderCustomerTeam = $('#tblOrderCustomerTeam').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            // drawCallback: function() {
            //     $('.select2-dt').select2();
            // },
            "ajax": {
                "type": "GET",
                "url": "/api/admin/order-teams/"+shared+"/customer",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name'},
                {data: 'rol'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": 0,
                "render": function(data, type, row){
                    if(row['Image']){
                        var avatar = '<img src="/img/users/'+row['Image']+'" class="img-circle mr-2" width="36">';    
                    }else{
                        var avatar = setAvatar(row['name'], row['surname']);
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

                    //Eliminar del pedido:
                    var eliminar = '';
                    if(row['rol'] == '' && row['customers_on']){
                        eliminar = '<form method="POST" action="/admin/budget-orders/'+shared+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<input type="hidden" name="member" value="'+row['id']+'">';
                            eliminar += '<input type="hidden" name="environment" value="customer">';
                            eliminar += '<button type="submit" name="submit_member" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtMiembroConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return show+' '+eliminar;    
                }
                }
            ]
        });
    }

    //Tabla Order Groups:
    if($('#tblOrderGroups').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var customer = $('#myCustomer').val();
        var year = $('#myYear').val();

        //Filters:
        $('#tblOrderGroups thead tr').clone(true).appendTo('#tblOrderGroups thead');
        $('#tblOrderGroups thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
     
            $('input', this).on('keyup change', function(){
                if(tableOrderGroups.column(i).search() !== this.value){
                    tableOrderGroups
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });
        
        var tableOrderGroups = $('#tblOrderGroups').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            drawCallback: function() {
                $('.select2-dt').select2();
            },
            "ajax": {
                "type": "GET",
                "url": "/api/admin/order-groups/"+customer+"/"+year,
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'id', className: 'text-right'},
                {data: 'id', className: 'text-right'},
                {data: 'name'},
                {data: 'alta', className: 'text-right'},
                //{data: 'businessArea'},
                //{data: 'costCenter'},
                //{data: 'workplace'},
                {data: 'estado'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": 0,
                "render": function(data, type, row){
                    var norder = '<a href="/admin/order-groups/'+row['id']+'/edit">'+row['id']+'</a>';
                    return norder;
                }
                },
                {"targets": 1,
                "render": function(data, type, row){
                    var old_num = row['old_order_group']? row['old_order_group']:'';
                    return old_num;    
                }
                },
                {"targets": -2,
                "render": function(data, type, row){
                    var arr = row['estado'].split(";");
                    var estado = '<select class="form-control select-xs comboEstadoGroup select2-dt">';
                        for(j=0; j<arr.length; j++){
                            if(j>0 && arr[j] != ''){
                                var p = arr[j].split('|');
                                var selected = p[0] == arr[0]? 'selected':'';
                                estado += '<option value="'+p[0]+'" data-id="'+row['id']+'" '+selected+'>'+p[1]+'</option>';
                            }
                        }
                    estado += '</select>';

                    return estado;
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    //Facturar:
                    var facturar = '';
                    if(row['invoice']){
                        facturar = '<a href="/admin/invoices/'+row['id']+'/create-from-group" class="btn btn-xs btn-info btn-confirm" rel="tooltip" title="'+txtFacturar+'" data-msg="'+txtFacturarConfirm+'"><i class="fa fa-file-invoice-dollar"></i></a>';
                    }
                    
                    //Editar:
                    var editar = '<a href="/admin/order-groups/'+row['id']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';

                    if(row['opened']){
                        //Eliminar:
                        var eliminar = '<form method="POST" action="/admin/order-groups/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtAgrupacionEliminar+'" data-msg="'+txtAgrupacionEliminarConfirm+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }else{
                        var eliminar = '';
                    }

                    return facturar+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Order Items:
    if($('#tblOrderItems').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var shared = $('#myShared').val();

        var tableOrderItems = $('#tblOrderItems').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            drawCallback: function() {
                $('.select2-dt').select2();
            },
            "ajax": {
                "type": "GET",
                "url": "/api/admin/order-items/"+shared,
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'id', className: 'text-right'},
                {data: 'concept', className: 'capitalize'},
                {data: 'qty', className: 'text-right'},
                {data: 'price', className: 'text-right'},
                {data: 'days', className: 'text-right'},
                {data: 'discount', className: 'text-right'},
                //{data: 'iva', className: 'text-right'},
                {data: 'amount', className: 'text-right'},
                {data: 'estado'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": 0,
                "render": function(data, type, row, meta){
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
                },
                {"targets": 3,
                "render": function(data, type, row){
                    return row['price']+row['currency'];
                }
                },
                {"targets": 4,
                "render": function(data, type, row){
                    if(row['days'] == 0){
                        return '';
                    }else{
                        return row['days'];    
                    }                    
                }
                },
                // {"targets": 6,
                // "render": function(data, type, row){
                //     if(row['iva']){
                //         return row['iva']+'%';    
                //     }else{
                //         return '';
                //     }
                // }
                // },
                {"targets": 6,
                "render": function(data, type, row){
                    var amount = setAmount(row['qty'], row['price'], row['discount'], row['days']);
                    if(amount){
                        return amount+row['currency'];
                    }else{
                        return '0'+row['currency'];
                    }
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    //Editar:
                    var editar = '';
                    if(row['editar']){
                        //Editar:
                        editar = '<a href="#" class="btn btn-xs btn-info btnEditItem" rel="tooltip" title="'+txtEditar+'" data-toggle="modal" data-target="#modal-edit-line" data-id="'+row['id']+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['editar']){
                        eliminar = '<form method="POST" action="/admin/items/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<input type="hidden" name="environment" value="orders">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtItemEliminar+'" data-msg="'+txtPedidoItemConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return editar+' '+eliminar;
                }
                }
            ],
            "footerCallback": function(row, data, start, end, display){
                //console.log(data);
                var api = this.api(), data;

                var currency = false;
                api.rows().every(function(){
                    var data = this.data();
                    if(!currency){
                        currency = data.currency;
                    }
                });

                if(!currency) currency = '€';

                //Separamos el importe de la moneda:
                var intVal = function(i){
                    return typeof i === 'string'? 
                    i.replace(/[\'+currency+',]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
                };

                //Total por filtro aplicado:
                amount = api
                .column(7)
                .data()
                .reduce(function(a, b){
                    return intVal(a) + intVal(b);
                }, 0);

                // $(api.column(7).footer()).html(
                //     total_amount.toFixed(2)+currency    
                // );
                

                // api.columns('.amount', {
                //     page: 'current'
                //   }).every(function() {
                //     var amount = this
                //       .data()
                //       .reduce(function(a, b) {
                //         var x = parseFloat(a) || 0;
                //         var y = parseFloat(b) || 0;
                //         return x + y;
                //       }, 0);
                //   });
                //   
                //console.log(amount)

                $('#totalAmountIva').text(amount.toFixed(2)+currency);
            }
        });
    }

    //Tabla Order Patterns:
    if($('#tblOrderPatterns').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblOrderPatterns').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/order-patterns",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name', className: 'capitalize'},
                {data: 'patron'},
                {data: 'id', className: 'text-center'},
                {data: 'alta', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": 2,
                "render": function(data, type, row){
                    if(row['yearly_reset'] == 1){
                        return '<i class="fa fa-check text-success"></i>';
                    }else{
                        return '<i class="fa fa-ban text-danger"></i>';
                    }
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    var estado = '';
                    var editar = '';
                    if(row['editar']){
                        //Estado:
                        var checked = (row['status'] == 1)? 'checked':'';
                        var estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtPatronOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+'><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="order-patterns/status"></span>';
                        estado += '</label>';

                        //Editar:
                        editar = '<a href="order-patterns/'+row['id']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['eliminar']){
                        eliminar = '<form method="POST" action="order-patterns/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtPatronPedidoConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Order Provider Team:
    if($('#tblOrderProviderTeam').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var shared = $('#myShared').val();

        var tableOrderProviderTeam = $('#tblOrderProviderTeam').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            // drawCallback: function() {
            //     $('.select2-dt').select2();
            // },
            "ajax": {
                "type": "GET",
                "url": "/api/admin/order-teams/"+shared+"/provider",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name'},
                {data: 'rol'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": 0,
                "render": function(data, type, row){
                    if(row['Image']){
                        var avatar = '<img src="/img/users/'+row['Image']+'" class="img-circle mr-2" width="36">';    
                    }else{
                        var avatar = setAvatar(row['name'], row['surname']);
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

                    //Eliminar del pedido:
                    var eliminar = '';
                    if(row['rol'] == '' && row['providers_on']){
                        eliminar = '<form method="POST" action="/admin/budget-orders/'+shared+'" class="frm-inline" role="form" accept-charset="UTF-8">';
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

    //Tabla Order Settings:
    if($('#tblOrderSettings').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblOrderSettings').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/order-settings",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name', className: 'capitalize'},
                {data: 'alta', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": -1,
                "render": function(data, type, row){
                    var checked = (row['status'] == 1)? 'checked':'';
                    var estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtPlantillaOnOff+'">';
                        estado += '<input type="checkbox" name="status" '+checked+' ><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="order-settings/status"></span>';
                    estado += '</label>';

                    //Editar:
                    var editar = '<a href="order-settings/'+row['id']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';

                    //Plantilla predefinida:
                    if(row['featured'] == 1){
                        var predefinida = '<i class="fa fa-star text-warning ml-1 mr-1" rel="tooltip" title="'+txtPlantillaPredefinida+'"></i>';
                    }else{
                        var predefinida = '<a href="order-settings/'+row['id']+'/featured" class="btn btn-xs btn-info" rel="tooltip" title="'+txtPlantillaPredefinir+'"><i class="fa fa-star"></i></a>';    
                    }                    

                    //Eliminar:
                    var eliminar = '<form method="POST" action="order-settings/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                        eliminar += '<input name="_method" type="hidden" value="DELETE">';
                        eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                        eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtPlantillaConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                    eliminar += '</form>';

                    return estado+' '+editar+' '+predefinida+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Orders:
    if($('#tblOrders').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var customer = $('#myCustomer').val() != ''? $('#myCustomer').val():false;
        var provider = $('#myProvider').val() != ''? $('#myProvider').val():false;
        var owner = $('#myOwner').val() && $('#myOwner').val() != ''? $('#myOwner').val():false;
        var status = $('#myStatus').val() && $('#myStatus').val() != ''? $('#myStatus').val():false;
        var accounting_on = $('#myModuleAccounting').val() == 1? true:false;

        //Filters:
        $('#tblOrders thead tr').clone(true).appendTo('#tblOrders thead');
        $('#tblOrders thead tr:eq(1) th').each(function(i){
            var title = $(this).text(); 
            if(i == 9){
                $(this).html('');
            }else{
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
                // aquí el código retirado
                
            }

            $('input', this).on('keyup change', function(){
                if(tableOrders.column(i).search() !== this.value){
                    tableOrders
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });
        
        var tableOrders = $('#tblOrders').DataTable({
            "order": [[ 0, "desc" ]],
            "serverSide": true,
            //"processing": true,
            drawCallback: function(){
                $('.select2-dt').select2();
            },
            "ajax": {
                "type": "GET",
                "url": "/api/admin/orders/"+customer+"/"+provider+"/"+owner+"/"+status,
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'order_num', className: 'text-right'},
                {data: 'order_old_num', className: 'text-right'},
                {data: 'budget', className: 'text-right'},
                {data: 'purchase_number', className: 'text-right'},
                {data: 'name'},
                {data: 'amount', className: 'text-right'},
                {data: 'fechaEntregaPrevista', className: 'text-right'},
                //{data: 'businessArea'},
                {data: 'costCenter'},
                //{data: 'workplace'},
                {data: 'estado'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": 0,
                "render": function(data, type, row){
                    var num = row['order_num']? row['order_num']:row['orderId'];
                    var norder = '<a target="_blank" href="/admin/orders/'+row['orderId']+'/edit">'+num+'</a>';
                    return norder;
                }
                },
                {"targets": 2,
                "render": function(data, type, row){
                    var budget = '<a target="_blank" href="/admin/budgets/'+row['budgetId']+'/edit">'+row['budget']+'</a>';
                    return budget;
                }
                },
                {"targets": -2,
                "render": function(data, type, row){
                    return '<span class="xoption-'+row['orderId']+'">'+row['estado']+'</span>'; 
                }
                }, 
                {"targets": -1,
                "render": function(data, type, row){
                    //Estado múltiple:
                    var estado = multipleStatus(row['estados'], row['status'], row['orderId']);

                    //Facturar:
                    var facturar = '';
                    if(accounting_on && row['invoiceable']){
                        //Facturas de agrupación:
                        if(row['og_id']){
                            facturar = '<a href="/admin/invoices/'+row['og_id']+'/by-order-group" class="btn btn-xs btn-success" rel="tooltip" title="'+txtFacturasAgrupacion+'"><i class="fa fa-layer-group"></i></a>';
                        
                        //Facturas simples:
                        }else{
                            if(row['invoiced']){
                                //Estado facturación:
                                if(row['invoices'] == '0'){
                                    var invoice_status = 'btn-info';
                                    var txtMsg = txtFacturar;
                                }else if(row['invoices'] == 1){
                                    var invoice_status = 'btn-warning';  
                                    var txtMsg = txtFacturaParcial;
                                }else if(row['invoices'] == 2){
                                    var invoice_status = 'btn-success';
                                    var txtMsg = txtFacturaCompleta;
                                }

                                facturar = '<a href="/admin/invoices/'+row['id']+'/by-order" class="btn btn-xs '+invoice_status+'" rel="tooltip" title="'+txtMsg+'"><i class="fa fa-file-invoice-dollar"></i></a>';

                            //Por facturar:
                            }else{
                                // facturar = '<a href="/admin/invoices/'+row['id']+'/create-from-order" class="btn btn-xs btn-info btn-confirm" rel="tooltip" title="'+txtFacturar+'" data-msg="'+txtFacturarConfirm+'"><i class="fa fa-file-invoice-dollar"></i></a>';

                                facturar = '<a href="#" class="btn btn-xs btn-info btnAddOrderToNewInvoice" rel="tooltip" title="'+txtFacturar+'" data-toggle="modal" data-target="#modal-generate-invoice" data-orderid="'+row['id']+'"><i class="fa fa-file-invoice-dollar"></i></a>';
                            }
                        }
                    }

                    //Editar:
                    var editar = '';
                    if(row['editar']){
                        var editar = '<a href="/admin/orders/'+row['orderId']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['eliminar']){
                        var eliminar = '<form method="POST" action="/admin/orders/'+row['orderId']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtPedidoConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+facturar+' '+editar+' '+eliminar;   
                }
                }
            ]
        });
    }

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
            "serverSide": true,
            /*"processing": true, */
            //scrollX: true,
            //fixedColumns: true,
            "ajax": {
                "type": "GET",
                "url": "/api/admin/permissions",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name'},
                {data: 'permission'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
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

    //Tabla Product Patterns:
    if($('#tblProductPatterns').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblProductPatterns').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/product-patterns",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name', className: 'capitalize'},
                {data: 'patron'},
                {data: 'alta', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": -1,
                "render": function(data, type, row){
                    //Estado:
                    if(row['editar']){
                        var checked = (row['status'] == 1)? 'checked':'';
                        estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+' ><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="product-patterns/status"></span>';
                        estado += '</label>';
                    }

                    //Editar:
                    var editar = '';
                    if(row['editar']){
                        editar = '<a href="product-patterns/'+row['id']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['eliminar']){
                        eliminar = '<form method="POST" action="product-patterns/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtPatronProductoConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Product Units:
    if($('#tblProductUnits').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var product_id = $('#productId').val();
        
        $('#tblProductUnits').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/product-units/"+product_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'unit', className: 'capitalize'},
                {data: 'symbol'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": -1,
                "render": function(data, type, row){
                    //Estado:
                    var estado = '<span style="display: none">0</span>';
                    if(row['editar']){
                        var checked = row['selected']? 'checked':'';
                        estado = '<label class="custom-chk-rdb btnSetProductUnit" rel="tooltip" title="'+txtUnidadAdoptar+'">';
                            estado += '<input type="checkbox" name="status" '+checked+' ><span class="chk chk-product-unit" data-productid="'+product_id+'" data-unitid="'+row['id']+'"></span>';
                        estado += '</label><span style="display: none">1</span>';
                    }else{
                        if(row['selected']){
                            estado = '<i class="fa fa-check text-success"></i><span style="display: none">1</span>';
                        }
                    }
                    return estado;
                }
                }
            ]
        });
    }

    //Tabla Products:
    if($('#tblProducts').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        //Filters:
        $('#tblProducts thead tr').clone(true).appendTo('#tblProducts thead');
        $('#tblProducts thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            // if(i == 3 || i == 5){
            //     $(this).html('');
            // }else{
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
            //}
     
            $('input', this).on('keyup change', function(){
                if(tableProducts.column(i).search() !== this.value){
                    tableProducts
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });
        
        var tableProducts = $('#tblProducts').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/products",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'producto'},
                {data: 'descripcion'},
                {data: 'price', className: 'text-right'},
                {data: 'id', className: 'text-center'},
                {data: 'id', className: 'text-center'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": 2,
                "render": function(data, type, row){
                    return row['price']+'€';    
                }
                }, 
                {"targets": 3,
                "render": function(data, type, row){
                    if(row['Image']){
                        return '<img src="/img/products/'+row['Image']+'" width="50"><span style="display: none">1</span>';      
                    }else{
                        return '<i class="fa fa-gift icono"></i><span style="display: none">0</span>';    
                    }                   
                }
                }, 
                {"targets": 4,
                "render": function(data, type, row){
                    if(row['on_sale']){
                        return '<i class="fa fa-check text-success"></i><span style="display: none">1</span>';
                    }else{
                        return '<i class="fa fa-ban text-danger"></i><span style="display: none">0</span>';
                    }
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    //Estado:
                    var estado = '';
                    if(row['editar']){
                        var checked = (row['status'] == 1)? 'checked':'';
                        var estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtProductoOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+' ><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="products/status"></span>';
                        estado += '</label>';
                    }

                    //Ver:
                    var ver = '<a href="/admin/products/'+row['id']+'/show" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtProductoVer+'"><i class="fa fa-eye"></i></a>';

                    //Translations:
                    // if(row['editar']){
                    //     var trans = linkTranslations(row['id'], 'products', txtTraducciones);
                    // }else{
                        //var trans = '';
                    //}

                    //Configuraciones de venta:
                    // var setups = '';
                    // if(row['on_sale']){
                    //     setups = '<a href="/admin/product-sales-setups/'+row['id']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtConfiguracionesVenta+'"><i class="fa fa-cogs"></i></a>';
                    // }

                    //Solicitar:
                    /*var solicita = '<a href="#" class="btn btn-xs btn-info btn-module btnOrderProduct" rel="tooltip" title="'+txtProductoPedir+'" data-toggle="modal" data-target="#modal-order-product" data-id="'+row['id']+'" data-product="'+row['name']+'"><i class="fa fa-shopping-basket"></i></a>';*/

                    //Editar:
                    var editar = '';
                    if(row['editar']){
                        var editar = '<a href="/admin/products/'+row['id']+'/edit" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtProductoEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['eliminar']){
                        var eliminar = '<form method="POST" action="/admin/products/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtProductoEliminar+'" data-msg="'+txtProductoConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+ver+' '+editar+' '+eliminar; //+' '+trans+' '+setups
                }
                } 
            ] 
        });
    }

    //Tabla Projects:
    if($('#tblProjects').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var status = $('#dtStatus').val();

        //Filters:
        $('#tblProjects thead tr').clone(true).appendTo('#tblProjects thead');
        $('#tblProjects thead tr:eq(1) th').each(function(i){
            var title = $(this).text(); 
            if(i == 5 || i == 6 || i == 8){
                $(this).html('');
            }else{
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
                // aquí el código retirado
                
            }

            $('input', this).on('keyup change', function(){
                if(tableProjects.column(i).search() !== this.value){
                    tableProjects
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });

        var tableProjects = $('#tblProjects').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/projects/"+status,
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name'},
                {data: 'id', className: 'text-right'},
                {data: 'id', className: 'text-right'},
                {data: 'id', className: 'text-right'},
                {data: 'hours_forecast', className: 'text-right'},
                {data: 'hours_worked'},
                {data: 'id', className: 'position-relative'},
                {data: 'estado'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": 0,
                "render": function(data, type, row){
                    return '<a href="/admin/projects/'+row['id']+'/edit">'+row['name']+'</a>';
                }
                },
                {"targets": 1,
                "render": function(data, type, row){
                    return '<span style="display: none">'+row['created_at']+'</span>'+row['alta'];
                }
                },
                {"targets": 2,
                "render": function(data, type, row){
                    return '<span style="display: none">'+row['estimated_delivery_date']+'</span>'+row['prevista'];
                }
                },
                {"targets": 3,
                "render": function(data, type, row){
                    return '<span style="display: none">'+row['delivery_date']+'</span>'+row['entrega'];
                }
                },
                {"targets": 5,
                "visible": false
                },
                {"targets": 6,
                "render": function(data, type, row){
                    var completed = row['completed'] > 100? 100:row['completed'];

                    var progress = '';
                    if(completed > 0){
                        progress += '<div class="tooltip-fixed"><span class="tooltiptext" style="left: calc('+completed+'% - 26px)">'+completed+'%</span></div>';
                    }
                    progress += '<div class="progress progress-mini  mx-1"><div style="width: '+completed+'%;" class="progress-bar"></div></div>';

                    return progress;
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
                    var estado = multipleStatus(row['estados'], row['status'], row['id']);

                    //Editar:
                    var editar = '';
                    if(row['editar']){
                        var editar = '<a href="/admin/projects/'+row['id']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['eliminar']){
                        var eliminar = '<form method="POST" action="/admin/projects/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtProyectoConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+editar+' '+eliminar;
                }
                }
            ],
            "footerCallback": function(row, data, start, end, display){
                var api = this.api(), data;

                var intVal = function(i){
                    return i;
                };

                //Total por filtro aplicado:
                hours_forecast = api
                .column(4, { search: 'applied' })
                .data()
                .reduce(function(a, b){
                    return intVal(a) + intVal(b);
                }, 0);

                //Horas trabajadas:
                hours_worked = api
                .column(5, { search: 'applied' })
                .data()
                .reduce(function(a, b){
                    return intVal(a) + intVal(b);
                }, 0);

                var pending = hours_forecast - hours_worked;

                $(api.column(4).footer()).html(
                    hours_forecast.toFixed(2)+' / '+pending.toFixed(2)
                );
            }
        });
    }

    //Tabla Providers:
    if($('#tblProviders').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        //Filters:
        $('#tblProviders thead tr').clone(true).appendTo('#tblProviders thead');
        $('#tblProviders thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 3){
                $(this).html('');
            }else{
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
            }
     
            $('input', this).on('keyup change', function(){
                if(tableProviders.column(i).search() !== this.value){
                    tableProviders
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });
        
        var tableProviders = $('#tblProviders').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/providers",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name'},
                {data: 'tradename'},
                {data: 'nif'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": -1,
                "render": function(data, type, row){
                    //Estado:
                    var estado = '';
                    //Responsables:
                    var users = '';
                    if(row['status_permission']){
                        var checked = (row['status'] == 1)? 'checked':'';
                        estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+' ><span class="chk chk-action" data-id="'+row['cp_id']+'" data-ctrl="customers/status"></span>';
                        estado += '</label>';

                        users = '<a href="#" class="btn btn-xs btn-info btn-module btnAddManager" rel="tooltip" title="'+txtResponsableAdd+'" data-toggle="modal" data-target="#modal-add-manager" data-id="'+row['id']+'" data-company="'+row['name']+'"><i class="fa fa-user-plus"></i></a>';
                    }

                    //Editar:
                    var editar = '';
                    if(!row['account'] || row['account']['guardian'] == row['current_company'] || row['edit']){
                        editar = '<a href="/admin/providers/'+row['id']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Ver:
                    var ver = '<a href="/admin/providers/'+row['cp_id']+'/show" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtVer+'"><i class="fa fa-eye"></i></a>';

                    //Nueva compra:
                    var compra = '';
                    var catalogo = '';
                    if(row['catalogue']){
                        compra = '<a href="/admin/orders/'+row['id']+'/purchase" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtCompraNueva+'"><i class="fa fa-cart-plus"></i></a>';
                    
                        catalogo = '<a href="/admin/products/'+row['id']+'/catalogue" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtCatalogoVer+'"><i class="fa fa-gift"></i></a>';
                    }
                    
                    //Eliminar:
                    var eliminar = '';
                    if(row['delete_permission']){
                        eliminar = '<form method="POST" action="customers/'+row['cp_id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<input type="hidden" name="tipo" value="provider">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtProveedorEliminar+'" data-msg="'+txtProveedorConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+editar+' '+ver+' '+compra+' '+catalogo+' '+users+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Provinces:
    if($('#tblProvinces').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var country_id = $('#countryId').val();
        
        $('#tblProvinces').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/provinces/"+country_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": -1,
                "render": function(data, type, row){
                    var estado = '';                    
                    var editar = '';
                    var eliminar = '';

                    if(row['editar']){
                        //Estado:
                        var checked = (row['status'] == 1)? 'checked':'';
                        estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtProvinciaOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+'><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="/admin/provinces/status"></span>';
                        estado += '</label>';
                    }

                    var towns = '';
                    if(row['ver']){
                        //Poblaciones:
                        towns = '<a href="/admin/towns/'+row['id']+'" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtPoblaciones+'"><i class="fa fa-city"></i></a>';
                    }

                    if(row['editar']){
                        //Editar:
                        editar = '<a href="#" class="btn btn-xs btn-info btn-module btnEditProvince" rel="tooltip" title="'+txtEditar+'" data-toggle="modal" data-target="#modal-edit-province" data-id="'+row['id']+'" data-province="'+row['name']+'" data-status="'+row['status']+'"><i class="fa fa-edit"></i></a>';

                        //Eliminar:
                        eliminar = '<form method="POST" action="/admin/provinces/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtProvinciaConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+towns+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Purchase Patterns:
    if($('#tblPurchasePatterns').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblPurchasePatterns').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/purchase-patterns",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name', className: 'capitalize'},
                {data: 'patron'},
                {data: 'id', className: 'text-center'},
                {data: 'alta', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": 2,
                "render": function(data, type, row){
                    if(row['yearly_reset'] == 1){
                        return '<i class="fa fa-check text-success"></i>';
                    }else{
                        return '<i class="fa fa-ban text-danger"></i>';
                    }
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    var estado = '';
                    var editar = '';
                    if(row['editar']){
                        //Estado:
                        var checked = (row['status'] == 1)? 'checked':'';
                        var estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtPatronOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+'><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="purchase-patterns/status"></span>';
                        estado += '</label>';

                        //Editar:
                        editar = '<a href="purchase-patterns/'+row['id']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['eliminar']){
                        eliminar = '<form method="POST" action="purchase-patterns/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtPatronOrdenCompraConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Question Answers:
    if($('#tblQuestionAnswers').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var question_id = $('#questionId').val();
        
        $('#tblQuestionAnswers').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/question-answers/"+question_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name'},
                {data: 'value', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": -1,
                "render": function(data, type, row){
                    if(row['editar']){
                        //Estado:
                        var checked = (row['status'] == 1)? 'checked':'';
                        var estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtRespuestaOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+'><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="/admin/answers/status"></span>';
                        estado += '</label>';

                        //Editar:
                        var editar = '<a href="#" data-toggle="modal" data-target="#modal-edit-answer" class="btn btn-xs btn-info btnEditAnswer" rel="tooltip" title="'+txtEditar+'" data-id="'+row['id']+'" data-name="'+row['name']+'" data-answer="'+row['answer']+'" data-points="'+row['points']+'" data-tf="'+row['is_true']+'" data-status="'+row['status']+'"><i class="fa fa-edit"></i></a>';

                        //Eliminar:
                        var eliminar = '<form method="POST" action="/admin/answers/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtRespuestaConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';

                        return estado+' '+editar+' '+eliminar;
                    
                    }else{
                        return '';
                    }
                }
                }
            ]
        });
    }

    //Tabla Questions:
    if($('#tblQuestions').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblQuestions').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/questions",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name'},
                {data: 'question'},
                {data: 'tipo'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": -1,
                "render": function(data, type, row){
                    var estado = '';
                    var editar = '';
                    var eliminar = '';

                    if(row['editar']){
                        //Estado:
                        var checked = (row['status'] == 1)? 'checked':'';
                        estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtCuestionOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+'><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="/admin/questions/status"></span>';
                        estado += '</label>';

                        //Editar:
                        editar = '<a href="/admin/questions/'+row['id']+'/edit" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['eliminar']){
                        eliminar = '<form method="POST" action="/admin/questions/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtCuestionConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Quiz Editions:
    if($('#tblQuizEditions').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        //Filters:
        $('#tblQuizEditions thead tr').clone(true).appendTo('#tblQuizEditions thead');
        $('#tblQuizEditions thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 3 || i == 7){
                $(this).html('');
            }else{
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
            }
     
            $('input', this).on('keyup change', function(){
                if(tableQuizEditions.column(i).search() !== this.value){
                    tableQuizEditions
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });
        
        var tableQuizEditions = $('#tblQuizEditions').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/quiz-editions",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name'},
                {data: 'quiz'},
                {data: 'alta', className: 'text-right'},
                {data: 'id', className: 'text-center'},
                {data: 'start_', className: 'text-right'},
                {data: 'finish_', className: 'text-right'},
                {data: 'time', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": 1,
                "render": function(data, type, row){
                    return '<a target="_blank" href="/admin/quizzes/'+row['quiz_id']+'/edit">'+row['quiz']+'</a>';
                }
                },
                {"targets": 2,
                "render": function(data, type, row){
                    return '<span style="display: none">'+row['created_at']+'</span>'+row['alta'];
                }
                },
                {"targets": 3,
                "render": function(data, type, row){
                    if(row['logo']){
                        var logo = '<img src="/img/quizzes/'+row['logo']+'" width="50">';
                    }else{
                        var logo = '<i class="fa fa-puzzle-piece icono"></i>';
                    }   
                    return logo;
                }
                },
                {"targets": 4,
                "render": function(data, type, row){
                    return '<span style="display: none">'+row['start']+'</span>'+row['start_'];
                }
                },
                {"targets": 5,
                "render": function(data, type, row){
                    return '<span style="display: none">'+row['finish']+'</span>'+row['finish_'];
                }
                },
                {"targets": 6,
                "render": function(data, type, row){
                    if(row['time'] > 0){
                        return row['time']/60+' '+txtMinutos;
                    }else{
                        return '';
                    }
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    //Editar:
                    var editar = '';
                    if(row['editar']){
                        editar = '<a href="/admin/quiz-editions/'+row['id']+'/edit" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = ''; 
                    if(row['eliminar']){
                        eliminar = '<form method="POST" action="/admin/quiz-editions/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtEdicionConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Quizzes:
    if($('#tblQuizzes').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblQuizzes').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/quizzes",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name'},
                {data: 'alta', className: 'text-right'},
                {data: 'description'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": 2,
                "render": function(data, type, row){
                    return row['description'];
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    var estado = '';
                    var editar = '';
                    if(row['editar']){
                        //Estado:
                        var checked = (row['status'] == 1)? 'checked':'';
                        var estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtConcursoOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+'><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="/admin/quizzes/status"></span>';
                        estado += '</label>';

                        //Editar:
                        editar = '<a href="/admin/quizzes/'+row['id']+'/edit" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['eliminar']){
                        eliminar += '<form method="POST" action="quizzes/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtConcursoConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Roles:
    if($('#tblRoles').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblRoles').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/roles",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name'},
                {data: 'description'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": -1,
                "render": function(data, type, row){
                    //Editar:
                    var editar = '';
                    if(row['roles_edit']){
                        editar = '<a href="/admin/roles/'+row['id']+'/edit" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['roles_destroy']){
                        eliminar += '<form method="POST" action="roles/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtRoleConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Shipment Patterns:
    if($('#tblShipmentPatterns').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblShipmentPatterns').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/shipment-patterns",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name', className: 'capitalize'},
                {data: 'patron'},
                {data: 'id', className: 'text-center'},
                {data: 'alta', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": 2,
                "render": function(data, type, row){
                    if(row['yearly_reset'] == 1){
                        return '<i class="fa fa-check text-success"></i>';
                    }else{
                        return '<i class="fa fa-ban text-danger"></i>';
                    }
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    var estado = '';
                    var editar = '';
                    if(row['editar']){
                        //Estado:
                        var checked = (row['status'] == 1)? 'checked':'';
                        var estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtPatronOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+'><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="shipment-patterns/status"></span>';
                        estado += '</label>';

                        //Editar:
                        editar = '<a href="shipment-patterns/'+row['id']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['eliminar']){
                        eliminar = '<form method="POST" action="shipment-patterns/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtPatronAlbaranConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Staff Picks:
    if($('#tblStaffPicks').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblStaffPicks').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/staff-picks",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name', className: 'capitalize'},
                {data: 'published_from', className: 'text-center'},
                {data: 'published_to', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": 0,
                "render": function(data, type, row){
                    if(row['name']){
                        return row['name'];
                    }else{
                        return row['id'];
                    }
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    var estado = '';
                    var editar = '';
                    if(row['editar']){
                        //Estado:
                        var checked = (row['status'] == 1)? 'checked':'';
                        var estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtPatronOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+'><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="staff-pick/status"></span>';
                        estado += '</label>';

                        //Editar:
                        editar = '<a href="staff-pick/'+row['id']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['eliminar']){
                        eliminar = '<form method="POST" action="shipment-patterns/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtPatronAlbaranConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Teams:
    if($('#tblTeams').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        $('#tblTeams').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/teams",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name'},
                {data: 'id', className: 'text-right'},
                {data: 'id', className: 'text-center'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": 1,
                "render": function(data, type, row){
                    return '<span style="display: none">'+row['created_at']+'</span>'+row['alta'];
                }
                },
                {"targets": 2,
                "render": function(data, type, row){
                    if(row['logo']){
                        var logo = '<img src="/img/teams/'+row['logo']+'" width="50">';
                    }else{
                        var logo = '<i class="fa fa-users icono"></i>';
                    }   
                    return logo;
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    var estado = '';
                    var editar = '';
                    var miembros = '';
                    if(row['editar']){
                        //Estado:
                        var checked = (row['status'] == 1)? 'checked':'';
                        var estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtEquipoOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+'><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="/admin/teams/status"></span>';
                        estado += '</label>';

                        //Editar:
                        editar = '<a href="/admin/teams/'+row['id']+'/edit" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';

                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['eliminar']){
                        eliminar += '<form method="POST" action="teams/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtEquipoConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Timetable Items:
    if($('#tblTimetableItems').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        $('#tblTimetableItems').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/timetable-items",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name'},
                {data: 'type'},
                {data: 'fixed', className: 'text-right'},
                {data: 'min_hour', className: 'text-right'},
                {data: 'min', className: 'text-right'},
                {data: 'max', className: 'text-right'},
                {data: 'qty', className: 'text-right'},
                {data: 'icon', className: 'text-center'},
                {data: 'color'},
                {data: 'reviewable', className: 'text-center'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": 0,
                "render": function(data, type, row){
                    return row['name'].charAt(0).toUpperCase()+row['name'].slice(1);
                }
                },
                {"targets": 1,
                "render": function(data, type, row){
                    if(row['type'] == 1){
                        return txtEntrada;
                    }else{
                        return txtSalida;
                    }
                }
                },
                {"targets": 2,
                "render": function(data, type, row){
                    if(row['fixed']){ 
                        return row['fixed']+'"';
                    }else{
                        return '';
                    } 
                }
                },
                {"targets": 3,
                "render": function(data, type, row){
                    if(row['min_hour']){
                        return row['min_hour'].substring(0, row['min_hour'].length-3)+'h';
                    }else{
                        return '';
                    }
                }
                },
                {"targets": 4,
                "render": function(data, type, row){
                    if(row['min']){ 
                        return row['min']+'"';
                    }else{
                        return '';
                    } 
                }
                },
                {"targets": 5,
                "render": function(data, type, row){
                    if(row['max']){ 
                        return row['max']+'"';
                    }else{
                        return '';
                    } 
                }
                },
                {"targets": 7,
                "render": function(data, type, row){
                    if(row['icon']){ 
                        return '<span class="fa fa-'+row['icon']+'"></span>';
                    }else{
                        return '';
                    }   
                }
                },
                {"targets": 8,
                "render": function(data, type, row){
                    if(row['color']){
                        return '<span class="colorEx mr-2" style="background-color: '+row['color']+'"></span> '+row['color'];   
                    }else{
                        return '';
                    }
                }
                },
                {"targets": 9,
                "render": function(data, type, row){
                    if(row['reviewable']){
                        return '<i class="fa fa-check text-success"></i>';
                    }else{
                        return '';    
                    }
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    //Editar:
                    var editar = '<a href="/admin/timetable-items/'+row['id']+'/edit" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';

                    //Eliminar:
                    var eliminar = '<form method="POST" action="timetable-items/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                        eliminar += '<input name="_method" type="hidden" value="DELETE">';
                        eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                        eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtConceptoConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                    eliminar += '</form>';

                    return editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Towns:
    if($('#tblTowns').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var province_id = $('#provinceId').val();
        
        $('#tblTowns').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/towns/"+province_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": -1,
                "render": function(data, type, row){
                    var estado = '';
                    var editar = '';
                    var eliminar = '';

                    if(row['editar']){
                        //Estado:
                        var checked = (row['status'] == 1)? 'checked':'';
                        estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtProvinciaOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+'><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="/admin/towns/status"></span>';
                        estado += '</label>';

                        //Editar:
                        editar = '<a href="#" class="btn btn-xs btn-info btn-module btnEditTown" rel="tooltip" title="'+txtEditar+'" data-toggle="modal" data-target="#modal-edit-town" data-id="'+row['id']+'" data-town="'+row['name']+'" data-status="'+row['status']+'"><i class="fa fa-edit"></i></a>';

                        //Eliminar:
                        eliminar = '<form method="POST" action="/admin/towns/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtPoblacionConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Units:
    if($('#tblUnits').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblUnits').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/units/",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name'},
                {data: 'symbol'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": -1,
                "render": function(data, type, row){
                    var estado = '';
                    var trans = '';
                    var editar = '';
                    if(row['editar']){
                        //Estado:
                        var checked = (row['status'] == 1)? 'checked':'';
                        estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtUnidadOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+'><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="/admin/units/status"></span>';
                        estado += '</label>';

                        //Translations:
                        trans = linkTranslations(row['id'], 'units', txtTraducciones);

                        //Editar:
                        editar = '<a href="/admin/units/'+row['id']+'/edit" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    var eliminar = '';
                    if(row['eliminar']){
                        //Eliminar:
                        eliminar = '<form method="POST" action="/admin/units/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtUnidadVentaConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+trans+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla User Categories:
    if($('#tblUserCategories').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblUserCategories').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/user-categories",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": -1,
                "render": function(data, type, row){
                    var estado = '';
                    var editar = '';
                    if(row['editar']){
                        //Estado:
                        var checked = (row['status'] == 1)? 'checked':'';
                        estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtCategoriaOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+' ><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="user-categories/status"></span>';
                        estado += '</label>';

                        //Editar:
                        editar = '<a href="/admin/user-categories/'+row['id']+'/edit" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['eliminar']){
                        eliminar = '<form method="POST" action="user-categories/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtCategoriaEliminar+'" data-msg="'+txtCategoriaConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla User Companies:
    if($('#tblUserCompanies').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var user = $('#myUser').val();
        var first_row = true;
        
        $('#tblUserCompanies').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/user-companies/"+user,
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": -1,
                "render": function(data, type, row){
                    //Estado:
                    var checked = row['activo']? 'checked':'';
                    var activo = row['activo']? 1:'0';
                    var estado = '';
                    //No se puede desvincular a un usuario que esté sólo vinculado a una empresa.
                    if(row['num_companies'] == 1 && first_row){
                        estado = '<i class="fa fa-check text-success mr-3"></i>';
                        first_row = false;
                        
                    }else{
                        estado = '<label class="custom-chk-rdb btnAddCompany" rel="tooltip" title="'+txtEmpresaVinculada+'">';
                            estado += '<input type="checkbox" name="status" '+checked+' ><span class="chk" data-companyid="'+row['id']+'" data-userid="'+row['user_id']+'"></span><span style="display: none">'+activo+'</span>';
                        estado += '</label>';    
                    }

                    return estado;
                }
                }
            ]
        });
    }

    //Tabla User Timetables:
    if($('#tblUserTimetables').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var from = $('#myFrom').val();
        var to = $('#myTo').val();
        var user = $('#myUser').val();

        $('#tblUserTimetables').DataTable({
            "serverSide": false,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/user-timetables/"+from+"/"+to+"/"+user,
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'date', className: 'text-right'},
                {data: 'hour', className: 'text-right'},
                {data: 'name'},
                {data: 'accumulated', className: 'text-right'},
                {data: 'comments'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": 0,
                //"render": $.fn.dataTable.render.moment( 'YYYY/MM/DD', 'DD-MM-YYYY' )
                "render": function(data, type, row){
                    var fecha = '<span style="display: none">'+row['date_order']+'</span>';
                    if(row['warning']){
                        fecha += '<span class="text-danger">'+row['date']+'</span>';
                    }else{
                        fecha += row['date'];
                    }
                    return fecha;
                }
                },
                {"targets": 2,
                "render": function(data, type, row){
                    var concept = '';
                    if(row['flag'] == 1){
                        concept += '<i class="fa fa-sign-in-alt text-success"></i> ';
                    }else if(row['flag'] == 2){
                        concept += '<i class="fa fa-sign-out-alt text-danger"></i> ';
                    }else{
                        concept += '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';   
                    }

                    if(row['warning']){
                        concept += '<span class="text-danger">'+row['name'].charAt(0).toUpperCase()+row['name'].slice(1)+'</span>';
                    }else{
                        concept += row['name'].charAt(0).toUpperCase()+row['name'].slice(1);
                    }
                    return concept;
                }
                },
                {"targets": 3,
                "render": function(data, type, row){
                    var accumulated = row['accumulated'];
                    if(row['accumulated'] != '' && row['hoursOfWork']){
                        accumulated += ' / '+row['hoursOfWork'];
                    }
                    if(row['diferencia']){
                        var clase = row['sign'] == '-'? 'text-danger':'text-success';
                        accumulated += ' <strong class="'+clase+' ml-2">'+row['diferencia']+'</strong>';    
                    }
                    return accumulated;
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    //Revisión:
                    var checked = row['revision'] == 1? 'checked':'';
                    var review = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtSolicitarRevision+'">';
                        review += '<input type="checkbox" name="revision" '+checked+'><span class="chk1 chkRev" data-id="'+row['id']+'"></span>';
                    review += '</label>';

                    //Editar:
                    if(row['edit_timetable']){
                        var edit = '<a href="#" class="btn btn-xs btn-info btnRegistroHorario" rel="tooltip" title="'+txtRegistroHorarioEditar+'" data-toggle="modal" data-target="#modal-edit-timetable" data-id="'+row['id']+'" data-concepto="'+row['name']+'" data-icono="'+row['icon']+'" data-flag="'+row['flag']+'">';
                            edit += '<i class="fa fa-edit"></i>';
                        edit += '</a>';
                    }else{
                        var edit = '';
                    }

                    //Añadir registro horario:
                    if(row['add_timetable']){
                        var create = '<a href="#" class="btn btn-xs btn-info btnAddRegistroHorario" rel="tooltip" title="'+txtRegistroHorarioAdd+'" data-toggle="modal" data-target="#modal-add-timetable" data-date="'+row['date']+'">';
                            create += '<i class="fa fa-plus"></i>';
                        create += '</a>';
                    }else{
                        var create = '';
                    }

                    //Eliminar registro horario:
                    if(row['edit_timetable']){
                        var eliminar = '<form method="POST" action="/admin/timetable/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtRegistroHorarioEliminar+'" data-msg="'+txtRegistroHorarioEliminarConfirm+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>'; 
                    }else{
                        var eliminar = '';
                    }

                    return review+' '+edit+' '+create+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Users:
    if($('#tblUsers').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var all = $('#all').val();
        var method = all? 'users-all':'users';

        //Filters:
        $('#tblUsers thead tr').clone(true).appendTo('#tblUsers thead');
        $('#tblUsers thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 6){
                $(this).html('');
            }else{
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
            }
     
            $('input', this).on('keyup change', function(){
                if(tableUsers.column(i).search() !== this.value){
                    tableUsers
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });
        
        var tableUsers = $('#tblUsers').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/"+method,
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'id'},
                {data: 'id', className: 'text-right'},
                {data: 'email'},
                {data: 'phones'},
                {data: 'categories'},
                {data: 'id', className: 'text-center'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": 0,
                "render": function(data, type, row){
                    return row['name']+' '+row['surname'];
                }
                },
                {"targets": 1,
                "render": function(data, type, row){
                    return '<span style="display: none">'+row['created_at']+'</span>'+row['alta'];
                }
                },
                {"targets": 2,
                "render": function(data, type, row){
                    return '<a target="_blank" href="mailto:'+row['email']+'">'+row['email']+'</a>';
                }
                },
                {"targets": 3,
                "render": function(data, type, row){
                    if(row['phones'].length > 0){
                        var phones = '';
                        for(j=0; j<row['phones'].length; j++){
                           phones += '<p style="line-height: 140%; margin-bottom: 0;">'+row['phones'][j].phone_number+'</p>';
                        }  
                        return phones;  
                    }else{
                        return '';
                    }
                }
                },
                {"targets": 5,
                "render": function(data, type, row){
                    if(row['Image']){
                        var avatar = '<img src="/img/users/'+row['Image']+'" class="img-circle" width="36">';    
                    }else{
                        var avatar = setAvatar(row['name'], row['surname']);
                    }
                    // if(row['Image']){
                    //     var image = '<img src="/img/users/'+row['Image']+'" class="img-circle" width="36">';
                    // }else{
                    //     var image = '<i class="fa fa-user icono"></i>';
                    //     //var image = '<img src="/img/users/'+row['avatar']+'" width="36">';
                    // }   
                    return avatar;    
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    //Estado:
                    var estado = '';
                    if(row['users_on']){
                        var checked = (row['status'] == 1)? 'checked':'';
                        estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtUsuarioOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+' ><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="users/status"></span>';
                        estado += '</label>';
                    }

                    //Mostrar:
                    var show = '';
                    if(row['users_show']){
                        show = '<a href="/admin/users/'+row['id']+'/show" class="btn btn-xs btn-info" rel="tooltip" title="'+txtVer+'"><i class="fa fa-eye"></i></a>';
                    }

                    //Vincular empresas:
                    var add_companies = '';
                    if(row['add_companies']){
                        add_companies = '<a href="/admin/user-companies/'+row['id']+'" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEmpresaVincular+'"><i class="fa fa-building"></i></a>';
                    }

                    //Editar:
                    var editar = '';
                    if(row['users_edit']){
                        editar = '<a href="/admin/users/'+row['id']+'/edit" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Notificar:
                    var notif = '<a href="#" class="btn btn-warning btn-xs btn-notif" rel="tooltip" title="Notificar" data-toggle="modal" data-target="#modal-new-notification" data-id="'+row['id']+'" data-user="'+row['name']+' '+row['surname']+'"><i class="fa fa-bell"></i></a>';

                    //Eliminar:
                    var eliminar = '';
                    if(row['users_on']){
                        eliminar = '<form method="POST" action="users/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtUsuarioEliminar+'" data-msg="'+txtUsuarioConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    //if(row['actions']){
                        return estado+' '+show+' '+add_companies+' '+editar+' '+eliminar;   //'+notif+'    
                    // }else{
                    //     return '';
                    // }                    
                }
                }
            ]
        });
    }

    //Tabla Vehicles:
    if($('#tblVehicles').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        //Filters:
        $('#tblVehicles thead tr').clone(true).appendTo('#tblVehicles thead');
        $('#tblVehicles thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 5){
                $(this).html('');
            }else{
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
            }
     
            $('input', this).on('keyup change', function(){
                if(tableVehicles.column(i).search() !== this.value){
                    tableVehicles
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });

        var tableVehicles = $('#tblVehicles').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/vehicles",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name'},
                {data: 'type'},
                {data: 'car_license'},
                {data: 'marca'},
                {data: 'model'},
                {data: 'id', className: 'text-center'},
                {data: 'alta', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": 0,
                "render": function(data, type, row){
                    var html = '';
                    if(row['editar']){
                        html += '<a href="/admin/vehicles/'+row['id']+'/edit">';
                    }
                    html += row['name'];
                    if(row['editar']){
                        html += '</a>';
                    }
                    return html;
                }
                },
                {"targets": 5,
                "render": function(data, type, row){
                    if(row['Image']){
                        var img = '<img src="/img/vehicles/'+row['Image']+'" width="36">';    
                    }else{
                        var img = '';
                    }
                    return img;    
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    var estado = '';
                    var editar = '';
                    var eliminar = '';
                    if(row['editar']){
                        //Estado:
                        var checked = (row['status'] == 1)? 'checked':'';
                        estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtVehiculoOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+' ><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="vehicles/status"></span>';
                        estado += '</label>';

                        //Editar:
                        editar = '<a href="/admin/vehicles/'+row['id']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';

                        //Eliminar:
                        eliminar = '<form method="POST" action="/admin/vehicles/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtVehiculoConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';

                        return '<span style="display: none">'+row['status']+'</span>'+estado+' '+editar+' '+eliminar;
                    }
                }
                }
            ]
        });
    }

    //Tabla Work Order Patterns:
    if($('#tblWorkOrderPatterns').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        $('#tblWorkOrderPatterns').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/work-order-patterns",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name', className: 'capitalize'},
                {data: 'patron'},
                {data: 'id', className: 'text-center'}, 
                {data: 'alta', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": 2,
                "render": function(data, type, row){
                    if(row['yearly_reset'] == 1){
                        return '<i class="fa fa-check text-success"></i>';
                    }else{
                        return '<i class="fa fa-ban text-danger"></i>';
                    }
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    //Estado:
                    var estado = '';
                    if(row['editar']){
                        var checked = (row['status'] == 1)? 'checked':'';
                        estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+' ><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="work-order-patterns/status"></span>';
                        estado += '</label>';
                    }

                    //Editar:
                    var editar = '';
                    if(row['editar']){
                        editar = '<a href="work-order-patterns/'+row['id']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['eliminar']){
                        eliminar = '<form method="POST" action="work-order-patterns/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtPatronTrabajoOrdenConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Work Order Products:
    if($('#tblWorkOrderProducts').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var work_order = $('#workOrderId').val();

         $('#tblWorkOrderProducts').DataTable({
            "serverSide": false,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/work-order-products/"+work_order,
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'id', className: 'text-right'},
                {data: 'name', className: 'capitalize'},
                {data: 'planned_qty', className: 'text-right'},
                {data: 'produced_qty', className: 'text-right'},
                {data: 'id', className: 'text-center'}, 
                {data: 'estado'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
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
                        editar = '<a href="#" class="btn btn-xs btn-info btnEditWorkOrderProduct" rel="tooltip" title="'+txtEditar+'" data-toggle="modal" data-target="#modal-edit-product" data-id="'+row['id']+'" data-product="'+row['productId']+'" data-plannedqty="'+row['planned_qty']+'" data-observations="'+row['observations']+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['editar']){
                        //Si el status de la orden permite eliminarla:
                        if(row['allow_delete']){
                            eliminar = '<form method="POST" action="/admin/work-order-products/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                                eliminar += '<input name="_method" type="hidden" value="DELETE">';
                                eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                                eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtTrabajoOrdenProductoConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                            eliminar += '</form>';
                        }
                    }

                    return estado+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

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
            "serverSide": true,
            /*"processing": true,*/
            drawCallback: function(){
                $('.select2-dt').select2();
            },
            "ajax": {
                "type": "GET",
                "url": "/api/admin/work-orders",
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'id', className: 'text-right'},
                {data: 'name'},
                {data: 'fechaEntregaPrevista', className: 'text-right'}, 
                {data: 'fechaEntrega', className: 'text-right'},
                {data: 'estado'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
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

    //Tabla Workplaces:
    if($('#tblWorkplaces').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var company_id = $('#companyId').val();
        var business_relationship = $('#businessRelationship').val();
        $('#companyId').remove();
        
        $('#tblWorkplaces').DataTable({
            "serverSide": true,
            /*"processing": true,*/
            "ajax": {
                "type": "GET",
                "url": "/api/admin/workplaces/"+company_id+"/"+business_relationship,
                "error": function(reason){
                    console.log(reason);
                }
            },
            "columns": [
                {data: 'name'},
                {data: 'Town'},
                {data: 'id', className: 'text-center'},
                {data: 'id', className: 'text-right'}
            ],
            "columnDefs": [
                {"targets": 2,
                "render": function(data, type, row){
                    if(row['logo']){
                        var logo = '<img src="/img/workplaces/'+row['logo']+'" width="50">';
                    }else{
                        var logo = '<i class="fa fa-building icono"></i>';
                    }   
                    return logo;
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    var estado = '';
                    var editar = '';
                    var principal = '';
                    if(row['editar']){
                        //Estado:
                        var checked = (row['status'] == 1)? 'checked':'';
                        if(row['featured'] != 1){
                            estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtCentroEstado+'">';
                                estado += '<input type="checkbox" name="status" '+checked+' ><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="/admin/workplaces/status"></span>';
                            estado += '</label>';
                        }
                    
                        //Editar:
                        var editar = '<a href="/admin/workplaces/'+row['id']+'/edit/'+row['br']+'" class="btn btn-xs btn-info btn-module" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';

                        //Centro principal:
                        if(row['featured'] == 1){
                            principal = '<i class="fa fa-star text-warning ml-1 mr-1" rel="tooltip" title="'+txtCentroPrincipal+'"></i>';
                        }else if(row['featured'] == 0 && row['status'] == 1){
                            principal = '<a href="/admin/workplaces/'+row['id']+'/'+row['br']+'/featured" class="btn btn-xs btn-info" rel="tooltip" title="'+txtCentroPrincipalMarcar+'"><i class="fa fa-star"></i></a>';    
                        }  
                    }   

                    //Eliminar:
                    var eliminar = '';
                    //Sólo se pueden eliminar centros secundarios, no el principal:
                    if(row['featured'] == 0 && row['eliminar']){
                        eliminar += '<form method="POST" action="/admin/workplaces/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<input type="hidden" name="business_relationship" value="'+row['br']+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtCentroEliminar+'" data-msg="'+txtCentroConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+editar+' '+principal+' '+eliminar;
                }
                }
            ]
        });
    }
});
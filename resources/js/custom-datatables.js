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
            "url": "/js/plugins/dataTables/lang/"+idm+".json"
        },
        responsive: true,
        stateSave: false,    //Guarda página al refrescar. 
        initComplete: function(){
            //Búsqueda automática al clicar sobre el td.filtro:
            var api = this.api();
            api.$('td.filtro').click(function(){
                api.search(this.innerHTML).draw();
            });

            this.api().columns().every(function(){
                var column = this;
                var input = document.createElement("input");
                // $(input).appendTo($(column.footer()).empty())
                // .on('change', function(){
                //     column.search($(this).val(), false, false, true).draw();
                // });
            });
        }
        // orderable: true,
        // searchable: true
    });

    $('.dTable').DataTable({
        stateSave: true,    //Guarda página al refrescar. 
    });

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

    //Tabla Attributes:
    if($('#tblAttributes').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        var tableAttributes = $('#tblAttributes').DataTable({
            serverSide: true,
            processing: true, 
            //scrollX: true,
            //fixedColumns: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/attributes",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'description'},
                {data: 'datatype'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }

    //Tabla Banks:
    if($('#tblBanks').length > 0){
        $('#tblBanks').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/banks",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'tradename'},
                {data: 'swift'},
                {data: 'eu_code'},
                {data: 'lei'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }

    //Tabla Batch Patterns:
    if($('#tblBatchPatterns').length > 0){
        $('#tblBatchPatterns').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/batch-patterns",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', className: 'capitalize'},
                {data: 'patron'},
                {data: 'reset', className: 'text-center'},
                {data: 'alta', className: 'text-right'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }

    //Tabla Batches:
    if($('#tblBatches').length > 0){
        var product_id = $('#myProduct').val();

        $('#tblBatches').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/batches/"+product_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'batch'},
                {data: 'product'},
                {data: 'fecha', className: 'text-right'},
                {data: 'expiry_date', className: 'text-right'},
                {data: 'work_order'},
                {data: 'initial_qty', className: 'text-right'},
                {data: 'acciones', className: 'text-right'}
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
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/budget-form-items/"+form,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
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
            serverSide: true,
            /*processing: true,*/
            ajax: {
                "type": "GET",
                "url": "/api/admin/budget-form-customers/"+form,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
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
            serverSide: true,
            /*processing: true, */
            //scrollX: true,
            //fixedColumns: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/budget-form-items/"+form+"/1",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'Bloque'},
                {data: 'price', className: 'text-right'},
                {data: 'discount', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
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
            serverSide: true,
            /*processing: true,*/
            ajax: {
                "type": "GET",
                "url": "/api/admin/budget-forms",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'alta', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
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
            var obvs = d.observations? d.observations:txtObservacionesSin;
            return '<table cellpadding="0" cellspacing="0" border="0">'+
                '<tr>'+
                    '<td colspan="8" class="no-border">'+obvs+'</td>'+
                '</tr>'+
            '</table>';
        }

        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var order = $('#myOrder').val();

        var tableBudgetItems = $('#tblBudgetItems').DataTable({
            serverSide: true,
            /*processing: true,*/
            drawCallback: function() {
                $('.select2-dt').select2();
            },
            ajax: {
                "type": "GET",
                "url": "/api/admin/order-items/"+order+"/"+environment,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
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
            columnDefs: [
                {"targets": 0,
                "render": function(data, type, row, meta){
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
                },
                // {"targets": 3,
                // "render": function(data, type, row){
                //     return row['price']+row['currency'];
                // }
                // },
                {"targets": 4,
                "render": function(data, type, row){
                    if(row['days'] == 0){
                        return '';
                    }else{
                        return row['days'];    
                    }                    
                }
                },
                // {"targets": 5,
                // "render": function(data, type, row){
                //     if(row['discount'] > 0){
                //         return row['discount']+'%';
                //     }else{
                //         return '';    
                //     }                    
                // }
                // },
                // {"targets": 6,
                // "render": function(data, type, row){
                //     var amount = setAmount(row['qty'], row['price'], row['discount'], row['days']);
                //     if(amount){
                //         return amount+row['currency'];
                //     }else{
                //         return '0'+row['currency'];
                //     }
                // }
                // },
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

        $('#tblBudgetItems tbody').on('click', 'td .btnSeeItem', function (e) {
            e.preventDefault();
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
            serverSide: true,
            /*processing: true,*/
            ajax: {
                "type": "GET",
                "url": "/api/admin/budget-patterns",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', className: 'capitalize'},
                {data: 'patron'},
                {data: 'id', className: 'text-center'},
                {data: 'alta', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
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
            serverSide: true,
            /*processing: true,*/
            ajax: {
                "type": "GET",
                "url": "/api/admin/budget-settings",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', className: 'capitalize'},
                {data: 'alta', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
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

        // Load data from localstorage
        var jsonData = JSON.parse(localStorage.getItem('DataTables_tblBudgets_'+window.location.pathname));

        // 01/07/2022: se ocultan temporalmente hasta dar el modo de que funcionen junto con ServerSide.
        $('#tblBudgets thead tr').clone(true).appendTo('#tblBudgets thead');
        $('#tblBudgets thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 5 || i == 10){
                $(this).html('');
            }else{
                // Check field on json of localstorage
                let value = jsonData && jsonData.columns[i].search.search ? jsonData.columns[i].search.search : '';
                let htmlRaw = `<input type="text" class="DTsearch" placeholder="${txtBuscar+' '+title}" value="${value}" />`
                $(this).html(htmlRaw);
                let element = $(this).html(htmlRaw);
            
                //Búsqueda por nombre y apellidos.
                if (i == 2) {
                    $('input', element).on('keyup change', function(){
                        // Diferenciamos si es nombre o apellido
                        let fullname = this.value.split(' ');

                        // Si es un solo nombre, se busca en el campo nombre dejando el campo apellido vacío
                        // caso contrario se busca en ambos campos
                        if(fullname.length == 1) {
                            // La busqueda se hace en el campo nombre
                            tableBudgets
                                .column(2)
                                .search(fullname[0])
                                .column(3)
                                .search('')
                                .draw();
                        }else{
                            // La busqueda se hace en ambos campos (nombre y apellido)
                            tableBudgets
                                .column(2)
                                .search(fullname[0])
                                .column(3)
                                .search(fullname[1])
                                .draw()
                        }
                    });
                }
            }
     
            $('input', this).on('keyup change', function(){
                // Ignoramos la busqueda del campo de nombres
                if (i == 2) {
                    return;
                }

                if(tableBudgets.column(i).search() !== this.value){
                    tableBudgets
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });

        var tableBudgets = $('#tblBudgets').DataTable({
            order: [[ 0, "desc" ]],
            serverSide: true,
            processing: true,
            stateSave: true,
            //retrieve: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/budgets/"+from,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'num', name: 'orders.budget_num', className: 'text-right'},
                {data: 'budget_title', name: 'orders.name'},
                {data: 'manager', name: 'users.name'},
                {data: 'surname', name: 'users.surname', className: 'd-none'},  //Columna oculta
                // {data: 'budget_old_num', name: 'orders.budget_old_num', className: 'text-right'},
                {data: 'order_num', name: 'orders.order_num'},
                {data: 'amount', name: 'amount', className: 'text-right'},
                // {data: 'modalidad', name: 'modalidad'},
                {data: 'alta', name: 'orders.created_at', className: 'text-right'},
                {data: 'company', name: 'companies.name', "defaultContent": ""},
                {data: 'final_customer', name: 'c_.name', "defaultContent": ""},
                {data: 'estado', name: 'orders.budget_status'},
                {data: 'acciones', name: 'acciones', orderable: false, searchable: false, className: 'text-right'}
            ],
            // Indicamos que columnas se tienen que ocultar
            columnDefs: [
                {targets: 3,
                visible: false,
                searchable: true}
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
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/business-areas/"+company_id+"/"+business_relationship,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
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

    //Tablas Cashes:
    if($('#tblCashes').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        var tableCashes = $('#tblCashes').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/cash",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'date', className: 'text-right'},
                {data: 'cash_center'},
                {data: 'customer'},
                {data: 'amount', className: 'text-right'},
                {data: 'payment_method'},
                {data: 'estado', name: 'estado'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }

    //Tabla Categories:
    if($('#tblCategories').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var module_id = $('#moduleId').val();

        var tableCategories = $('#tblCategories').DataTable({
            serverSide: true,
            processing: true,
            "language": {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
            },
            ajax: {
                "type": "GET",
                "url": "/api/admin/categories/"+module_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', name: 'name'},
                {data: 'level', name: 'level'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }

    //Tabla Companies:
    if($('#tblCompanies').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        //Filters:

        // Load data from localstorage
        var jsonData = JSON.parse(localStorage.getItem('DataTables_tblCompanies_'+window.location.pathname));

        $('#tblCompanies thead tr').clone(true).appendTo('#tblCompanies thead');
        $('#tblCompanies thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 5 || i == 6){
                $(this).html('');
            }else{
                // Check field on json of localstorage
                let value = jsonData && jsonData.columns[i].search.search ? jsonData.columns[i].search.search : '';
                let htmlRaw = `<input type="text" class="DTsearch" placeholder="${txtBuscar+' '+title}" value="${value}" />`
                $(this).html(htmlRaw);

                // $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
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
            serverSide: true,
            processing: true,
            stateSave: true,
            "language": {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
            },
            ajax: {
                "type": "GET",
                "url": "/api/admin/companies",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'tradename'},
                {data: 'alta', className: 'text-right'},
                {data: 'nif'},
                {data: 'ute', className: 'text-center'},
                {data: 'logo', className: 'text-center'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }

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

    //Tabla Company Sectors:
    if($('#tblCompanySectors').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var company_id = $('#dtCompanyId').val();
        
        $('#tblCompanySectors').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/company-sectors/"+company_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'level'},
                {data: 'acciones', className: 'text-right'}
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
            serverSide: true,
            /*processing: true,*/
            ajax: {
                "type": "GET",
                "url": "/api/admin/contents",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'code'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
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
            serverSide: true,
            /*processing: true,*/
            ajax: {
                "type": "GET",
                "url": "/api/admin/contracts",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'alta', className: 'text-right'},
                {data: 'duration', className: 'text-right'},
                {data: 'with_holidays', className: 'text-center'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
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
            serverSide: true,
            /*processing: true,*/
            ajax: {
                "type": "GET",
                "url": "/api/admin/cost-centers/"+company_id+"/"+business_relationship,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
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
            serverSide: true,
            /*processing: true,*/
            ajax: {
                "type": "GET",
                "url": "/api/admin/countries",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'code'},
                {data: 'alfa2'},
                {data: 'alfa3'},
                {data: 'flag'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
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
            serverSide: false,
            /*processing: true,*/
            ajax: {
                "type": "GET",
                "url": "/api/admin/currencies",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'code'},
                {data: 'number', className: 'text-right'},
                {data: 'symbol', className: 'text-center'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
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
            if(i == 2 || i == 4 || i == 5){
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
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/customer-products/"+customer+"/"+ba,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', name: 'products.name'},
                {data: 'descripcion', name:"products.description"},
                {data: 'image', className: 'text-center', orderable: false, searchable: false},
                {data: 'price', name: 'products.price', className: 'text-right'},
                {data: 'customer_price', className: 'text-right', orderable: false, searchable: false},
                {data: 'acciones', className: 'text-right', orderable: false, searchable: false}
            ]
        });
    }

    //Tabla Customer Providers Users:
    if($('#tblCustomerProvidersUsers').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var company = $('#dtCompanyId').val();

        var tableCustomerProvidersUsers = $('#tblCustomerProvidersUsers').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/customer-providers-users/"+company,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'user', name: "users.name"},
                {data: 'categories'},
                {data: 'email', name: "users.email"},
                {data: 'phones', name: "phones.phone_number"},
                {data: 'image', className: 'text-center'},
                {data: 'acciones', name: "users.id", className: 'text-right'}
            ]
        });
    }

    //Tabla Customers:
    if($('#tblCustomers').length > 0){ 
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        //Filters:

        // Load data from localstorage
        var jsonData = JSON.parse(localStorage.getItem('DataTables_tblCustomers_'+window.location.pathname));
        
        $('#tblCustomers thead tr').clone(true).appendTo('#tblCustomers thead');
        $('#tblCustomers thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 3){
                $(this).html('');
            }else{
                // Check field on json of localstorage
                let value = jsonData && jsonData.columns[i].search.search ? jsonData.columns[i].search.search : '';
                let htmlRaw = `<input type="text" class="DTsearch" placeholder="${txtBuscar+' '+title}" value="${value}" />`
                $(this).html(htmlRaw);

                //$(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'" '+htmlvalue+'/>');        
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
            order: [[ 0, "desc" ]],
            serverSide: true,
            processing: true,
            stateSave: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/customers",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', name: 'companies.name'},
                {data: 'tradename', name: 'companies.tradename'},
                {data: 'nif', name: 'companies.nif'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }

    //Tabla Day Types:
    if($('#tblDayTypes').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        $('#tblDayTypes').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/day-types",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'color'},
                {data: 'is_working', className: 'text-center'},
                {data: 'user_enabled', className: 'text-center'},
                {data: 'cumulative', className: 'text-center'},
                {data: 'modifiable', className: 'text-center'},
                {data: 'repetition', className: 'text-center'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }

    //Tabla Deliveries:
    if($('#tblDeliveries').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var customer = $('#myCustomer').val() != ''? $('#myCustomer').val():false;
        var year = $('#myYear').val();
        var status = $('#myStatus').val() && $('#myStatus').val() != ''? $('#myStatus').val():false;

        //Filters:
        $('#tblDeliveries thead tr').clone(true).appendTo('#tblDeliveries thead');
        $('#tblDeliveries thead tr:eq(1) th').each(function(i){
            var title = $(this).text(); 
            if(i == 2 || i == 5){
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
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/deliveries/"+year+"/"+customer+"/"+status,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'num', className: 'text-right'},
                {data: 'name'},
                {data: 'amount', className: 'text-right'},
                {data: 'date', className: 'text-right'},
                {data: 'status'},
                {data: 'acciones', className: 'text-right'}
            ]
            // columnDefs: [
            //     {"targets": 0,
            //     "render": function(data, type, row){
            //         var num = row['id'];
            //         if(row['delivery_num']){
            //             num = row['delivery_num'];
            //         }else if(row['delivery_old_num']){
            //             num = row['delivery_old_num'];
            //         }

            //         var html = '';
            //         if(row['editar']){
            //             html += '<a href="/admin/deliveries/'+row['id']+'/edit">';
            //         }
            //         html += num;
            //         if(row['editar']){
            //             html += '</a>';
            //         }
            //         return html;
            //     }
            //     },
            //     {"targets": -2,
            //     "render": function(data, type, row){
            //         return '<span class="xoption-'+row['id']+'">'+row['estado']+'</span>'; 
            //     }
            //     }, 
            //     {"targets": -1,
            //     "render": function(data, type, row){
            //         var estado = '';
            //         var editar = '';
            //         var eliminar = '';
            //         if(row['editar']){
            //             //Estado múltiple:
            //             estado = multipleStatus(row['estados'], row['status'], row['id']);

            //             //Editar:
            //             editar = '<a href="/admin/deliveries/'+row['id']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';

            //             //Eliminar:
            //             eliminar = '<form method="POST" action="/admin/deliveries/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
            //                 eliminar += '<input name="_method" type="hidden" value="DELETE">';
            //                 eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
            //                 eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtEntregaConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
            //             eliminar += '</form>';

            //             return estado+' '+editar+' '+eliminar;
            //         }
            //     }
            //     }
            // ]
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
            serverSide: true,
            processing: true,
            drawCallback: function() {
                $('.select2-dt').select2();
            },
            ajax: {
                "type": "GET",
                "url": "/api/admin/deliveries-orders",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'num', className: 'text-right'},
                {data: 'name'},
                {data: 'company'},
                {data: 'id'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
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
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/delivery-patterns",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', className: 'capitalize'},
                {data: 'patron'},
                {data: 'id', className: 'text-center'},
                {data: 'alta', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
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

    //Tabla Edition Companies:
    if($('#tblEditionCompanies').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var edition_id = $('#editionId').val();
        
        $('#tblEditionCompanies').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/quiz-edition-companies/"+edition_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', className: 'capitalize'},
                {data: 'created_at', className: 'text-right'},
                {data: 'competitors', className: 'text-right'},
                {data: 'acciones', className: 'text-right'}
            ]  
        });
    }

    //Tabla Edition Teams:
    if($('#tblEditionTeams').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var edition_id = $('#editionId').val();
        
        $('#tblEditionTeams').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/quiz-edition-teams/"+edition_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', className: 'capitalize'},
                {data: 'logo', className: 'text-center'},
                {data: 'points', className: 'text-right'},
                {data: 'time', className: 'text-right'},
                {data: 'acciones', className: 'text-right'}
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

    //Tabla Employees Hours:
    if($('#tblEmployeesHours').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var workplace_id = $('#dtWorkplace').val();
        var from = $('#dtFrom').val();
        var to = $('#dtTo').val();

        var tblEmployeesHours = $('#tblEmployeesHours').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/employees-timetable/"+workplace_id+"/"+from+"/"+to,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'employee'},
                {data: 'worked_hours', className: 'text-right'},
                {data: 'working_hours', className: 'text-right'},
                {data: 'difference', className: 'text-right'},
                {data: 'situation', className: 'text-center'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }

    //Tabla Expenses:
    if($('#tblExpenses').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        var tableExpenses = $('#tblExpenses').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/expenses",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'concept'},
                {data: 'expensed_in', className: 'text-right'},
                {data: 'amount', className: 'text-right'},
                {data: 'type'},
                {data: 'provider'},
                {data: 'project', className: 'text-right'},
                {data: 'doc', className: 'text-center'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }

    //Tabla GroupMembers:
    if($('#tblGroupMembers').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var group_id = $('#groupId').val();
        
        $('#tblGroupMembers').DataTable({
            serverSide: true,
            /*processing: true,*/
            ajax: {
                "type": "GET",
                "url": "/api/admin/group-members/"+group_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'rol'},
                {data: 'alta', className: 'text-right'},
                {data: 'acciones', className: 'text-right'}
            ]
            // columnDefs: [
            //     {"targets": 0,
            //     "render": function(data, type, row){
            //         return row['name']+' '+row['surname'];
            //     }
            //     },
            //     {"targets": -1,
            //     "render": function(data, type, row){
            //         //Estado:
            //         var checked = (row['responsible'] == 1)? 'checked':'';
            //         var responsible = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtGrupoResponsable+'">';
            //             responsible += '<input type="checkbox" name="responsible" '+checked+' ><span class="chk" data-id="'+row['id']+'" data-ctrl="/admin/group_members/responsible"></span>';
            //         responsible += '</label>';

            //         //Editar:
            //         var editar = '<a href="#" data-toggle="modal" data-target="#modal-edit-member" data-id="'+row['id']+'" data-member="'+row['name']+' '+row['surname']+'" data-rol="'+row['rol']+'" class="btn btn-xs btn-info btn-module btnEditMember" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';

            //         //Eliminar:
            //         var eliminar = '<form method="POST" action="/admin/group_members/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
            //             eliminar += '<input name="_method" type="hidden" value="DELETE">';
            //             eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
            //             eliminar += '<input type="hidden" name="group_id" value="'+row['group_id']+'">';
            //             eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtMiembroConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
            //         eliminar += '</form>';

            //         return responsible+' '+editar+' '+eliminar;
            //     }
            //     }
            // ]
        });
    }

    //Tabla Groups:
    if($('#tblGroups').length > 0){
        //var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblGroups').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/groups",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'alta', name: 'alta', className: 'text-right'},
                {data: 'description'},
                {data: 'acciones', name: 'acciones', className: 'text-right'}
            ]
        });
    }

    //Tabla Invoice Control:
    if($('#tblInvoiceControl').length > 0){
        $('#tblInvoiceControl').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/invoice-control",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'order_num', name: 'orders.order_num', className: 'text-right'},
                {data: 'company', name: 'companies.name'},
                {data: 'manager', name: 'users.name'},
                {data: 'delivery_date', className: 'text-right'},
                {data: 'amount', name: 'invoices.amount', className: 'text-right'},
                {data: 'pendiente', className: 'text-right'},
                {data: 'estado'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }

    //Tabla Invoice Items:
    if($('#tblInvoiceItems').length > 0){
        function format(d){
            // `d` is the original data object for the row
            var obvs = d.observations? d.observations:txtObservacionesSin;
            return '<table cellpadding="0" cellspacing="0" border="0">'+
                '<tr>'+
                    '<td colspan="10" class="no-border">'+obvs+'</td>'+
                '</tr>'+
            '</table>';
        }

        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var order = $('#myOrder').val();
        var environment = 'invoices';

        var tableInvoiceItems = $('#tblInvoiceItems').DataTable({
            serverSide: true,
            processing: true,
            // drawCallback: function() {
            //     $('.select2-dt').select2();
            // },
            ajax: {
                "type": "GET",
                "url": "/api/admin/order-items/"+order+"/"+environment,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'id', className: 'text-right'},
                {data: 'concept', className: 'capitalize'},
                {data: 'qty', className: 'text-right'},
                {data: 'price', className: 'text-right'},
                {data: 'iva', className: 'text-right'},
                {data: 'days', className: 'text-right'},
                {data: 'discount', className: 'text-right'},
                {data: 'amount', className: 'text-right'},
                {data: 'cost_center'},
                {data: 'estado'},
                {data: 'acciones', className: 'text-right'}
            ],
            columnDefs: [
                {"targets": 0,
                "render": function(data, type, row, meta){
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
                },
                {"targets": 5,
                "render": function(data, type, row){
                    if(row['days'] < 2){
                        return '';
                    }else{
                        return row['days'];    
                    }                    
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
                .column(8)
                .data()
                .reduce(function(a, b){
                    return intVal(a) + intVal(b);
                }, 0);

                // $(api.column(8).footer()).html(
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

        $('#tblInvoiceItems tbody').on('click', 'td .btnSeeItem', function (e) {
            e.preventDefault();
            var tr = $(this).closest('tr');
            var row = tableInvoiceItems.row(tr);
     
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

    //Tabla Invoice Patterns:
    if($('#tblInvoicePatterns').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblInvoicePatterns').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/invoice-patterns",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', className: 'capitalize'},
                {data: 'patron'},
                {data: 'id', className: 'text-center'},
                {data: 'alta', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
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
            serverSide: false,
            /*processing: true,*/
            ajax: {
                "type": "GET",
                "url": "/api/admin/invoice-settings",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', className: 'capitalize'},
                {data: 'alta', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
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

    //Tabla Invoices:
    if($('#tblInvoices').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var customer = $('#myCustomer').val() != ''? $('#myCustomer').val():'0';
        var provider = $('#myProvider').val() != ''? $('#myProvider').val():'0'; 
        var year = $('#myYear').val();
        var month = $('#myMonth').val();
        var orderId = $('#orderId').val();
        var poId = $('#poId').val();        //by purchase order.

        //Filters:

        // Load data from localstorage
        var jsonData = JSON.parse(localStorage.getItem('DataTables_tblInvoices_'+window.location.pathname));

        $('#tblInvoices thead tr').clone(true).appendTo('#tblInvoices thead');
        $('#tblInvoices thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 10){
                $(this).html('');
            }else{
                // Check field on json of localstorage
                let value = jsonData && jsonData.columns[i].search.search ? jsonData.columns[i].search.search : '';
                let htmlRaw = `<input type="text" class="DTsearch" placeholder="${txtBuscar+' '+title}" value="${value}" />`
                $(this).html(htmlRaw);

                // $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
            }
     
            $('input', this).on('keyup change', function(){
                if(tableInvoices.column(i).search() !== this.value){
                    tableInvoices
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });

        // var api_route = 'invoices/'+year+'/'+customer+'/'+provider;
        var api_route = `invoices/${year}/${customer}/${provider}/${month}`;

        if(orderId){
            api_route = 'invoices-by-order/'+orderId;
        }else if(poId){
            api_route = 'invoices-by-purchase/'+poId;
        }
        
        var tableInvoices = $('#tblInvoices').DataTable({
            order: [[ 4, "desc" ]],
            serverSide: false,
            processing: false,
            stateSave: true,
            autoWidth: false,
            // drawCallback: function(){
            //     $('.select2-dt').select2();
            // },
            ajax: {
                "type": "GET",
                "url": "/api/admin/"+api_route,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'invoice_num', name: 'invoices.invoice_num', className: 'text-right'},
                {data: 'company', name: 'companies.name'},
                {data: 'order', name: 'orders.order_num', className: 'text-right'},
                // {data: 'manager', name: 'users.name'},
                {data: 'fiscal_date', className: 'text-right'},
                {data: 'expiration_date', className: 'text-right'},
                {data: 'amount', name: 'invoices.amount', className: 'text-right'},
                {data: 'amount_with_iva', name: 'invoices.amount_with_iva', className: 'text-right'},
                {data: 'payment_method', name: 'payment_methods.method'},
                {data: 'month'},
                //{data: 'workplace'},
                {data: 'estado'},
                {data: 'acciones', className: 'text-right'}
            ],
            "footerCallback": function(row, data, start, end, display){
                // if(typeof year != 'undefined'){
                    var api = this.api(), data;
                    var json = api.ajax.json();

                    var currency = false;
                    var refund = false;
                    api.rows().every(function(){
                        var data = this.data();
                    });

                    if(!currency) currency = '€';
     
                    // Remove the formatting to get integer data for summation
                    var intVal = function(i){
                        //console.log(i);
                        return typeof i === 'string'? 
                        i.replace(/[\'+currency+',]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                    };

                    //Total:
                    total = api
                    .column(5, { search: 'applied' })
                    .data()
                    .reduce(function(a, b){
                        return intVal(a) + intVal(b);
                    }, 0);

                    $(api.column(5).footer()).html(
                        total.toFixed(2)+currency   
                    );

                    //Total con IVA:
                    total_iva = api
                    .column(6, { search: 'applied' })
                    .data()
                    .reduce(function(a, b){
                        return intVal(a) + intVal(b);
                    }, 0);

                    $(api.column(6).footer()).html(
                        total_iva.toFixed(2)+currency   
                    );
     
                    //Total por filtro aplicado:
                    // total_amount = api
                    // .column(5, { search: 'applied' })
                    // .data()
                    // .reduce(function(a, b){
                    //     return intVal(a) + intVal(b);
                    // }, 0);

                    // $(api.column(5).footer()).html(
                    //     total_amount.toFixed(2)+currency    
                    // );
                //}
            }
        });
    }

    //Tabla IP Access:
    if($('#tblIpAccess').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblIpAccess').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/ip-access",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'group'},
                {data: 'ip_start'},
                {data: 'ip_end'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
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
            serverSide: true,
            /*processing: true,*/
            ajax: {
                "type": "GET",
                "url": "/api/admin/modules",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'module', className: 'capitalize'},
                {data: 'nivel'},
                {data: 'id', className: 'text-center'},
                {data: 'id', className: 'text-center'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
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
            serverSide: true,
            /*processing: true,*/
            ajax: {
                "type": "GET",
                "url": "/api/admin/notifications-unread",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'id', className: 'capitalize'},
                {data: 'id'},
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
            ],
            columnDefs: [
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

                    //Eliminar miembro del equipo:
                    var eliminar = '';
                    if(row['rol'] == '' && row['customers_on']){
                        eliminar = '<form method="POST" action="/admin/orders/'+order+'/remove-member" class="frm-inline" role="form" accept-charset="UTF-8">';
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
        // $('#tblOrderGroups thead tr').clone(true).appendTo('#tblOrderGroups thead');
        // $('#tblOrderGroups thead tr:eq(1) th').each(function(i){
        //     var title = $(this).text();
        //     if(i == 7){
        //         $(this).html('');
        //     }else{
        //         $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
        //     }
     
        //     $('input', this).on('keyup change', function(){
        //         if(tableOrderGroups.column(i).search() !== this.value){
        //             tableOrderGroups
        //                 .column(i)
        //                 .search(this.value)
        //                 .draw();
        //         }
        //     });
        // });
        
        var tableOrderGroups = $('#tblOrderGroups').DataTable({
            serverSide: true,
            processing: true,
            // drawCallback: function() {
            //     $('.select2-dt').select2();
            // },
            ajax: {
                "type": "GET",
                "url": "/api/admin/order-groups/"+customer+"/"+year,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'num', name: 'num', className: 'text-right'},
                {data: 'order_old_num', name: 'order_old_num', className: 'text-right'},
                {data: 'name', name: 'orders.name'},
                {data: 'purchase', name: 'purchase'},
                {data: 'alta', name: 'alta', className: 'text-right'},
                {data: 'group_amount', className: 'text-right'},
                {data: 'bag_amount', className: 'text-right'},
                //{data: 'costCenter'},
                //{data: 'workplace'},
                {data: 'estado', name: 'estado'},
                {data: 'acciones', name: 'acciones', className: 'text-right'}
            ]
        });
    }

    //Tabla Order Items:
    if($('#tblOrderItems').length > 0){
        function format(d){
            // `d` is the original data object for the row
            var obvs = d.observations? d.observations:txtObservacionesSin;
            return '<table cellpadding="0" cellspacing="0" border="0">'+
                '<tr>'+
                    '<td colspan="9" class="no-border">'+obvs+'</td>'+
                '</tr>'+
            '</table>';
        }

        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var order = $('#myOrder').val();
        var environment = $('#myEnvironment').val();

        var tableOrderItems = $('#tblOrderItems').DataTable({
            serverSide: true,
            processing: true,
            // drawCallback: function() {
            //     $('.select2-dt').select2();
            // },
            ajax: {
                "type": "GET",
                "url": "/api/admin/order-items/"+order+"/"+environment,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'id', className: 'text-right'},
                {data: 'concept', className: 'capitalize'},
                {data: 'qty', className: 'text-right'},
                {data: 'price', className: 'text-right'},
                {data: 'days', className: 'text-right'},
                {data: 'discount', className: 'text-right'},
                {data: 'amount', className: 'text-right'},
                {data: 'cost_center'},
                {data: 'estado'},
                {data: 'acciones', className: 'text-right'}
            ],
            columnDefs: [
                {"targets": 0,
                "render": function(data, type, row, meta){
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
                },
                {"targets": 4,
                "render": function(data, type, row){
                    if(row['days'] < 2){
                        return '';
                    }else{
                        return row['days'];    
                    }                    
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
                .column(8)
                .data()
                .reduce(function(a, b){
                    return intVal(a) + intVal(b);
                }, 0);

                // $(api.column(8).footer()).html(
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

        $('#tblOrderItems tbody').on('click', 'td .btnSeeItem', function (e) {
            e.preventDefault();
            var tr = $(this).closest('tr');
            var row = tableOrderItems.row(tr);
     
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

    //Tabla Order Items Show:
    if($('#tblOrderItemsShow').length > 0){
        function format(d){
            // `d` is the original data object for the row
            var obvs = d.observations? d.observations:txtObservacionesSin;
            return '<table cellpadding="0" cellspacing="0" border="0">'+
                '<tr>'+
                    '<td colspan="7" class="no-border">'+obvs+'</td>'+
                '</tr>'+
            '</table>';
        }

        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var order = $('#myOrder').val();

        var tableOrderItemsShow = $('#tblOrderItemsShow').DataTable({
            serverSide: true,
            processing: true,
            // drawCallback: function() {
            //     $('.select2-dt').select2();
            // },
            ajax: {
                "type": "GET",
                "url": "/api/admin/budget-items/"+order,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'id', className: 'text-right'},
                {data: 'concept', className: 'capitalize'},
                {data: 'qty', className: 'text-right'},
                {data: 'price', className: 'text-right'},
                {data: 'days', className: 'text-right'},
                {data: 'discount', className: 'text-right'},
                {data: 'amount', className: 'text-right'},
                {data: 'acciones', className: 'text-right'}
            ],
            columnDefs: [
                {"targets": 0,
                "render": function(data, type, row, meta){
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
                },
                {"targets": 4,
                "render": function(data, type, row){
                    if(row['days'] < 2){
                        return '';
                    }else{
                        return row['days'];    
                    }                    
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

                // $(api.column(6).footer()).html(
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

        $('#tblOrderItemsShow tbody').on('click', 'td .btnSeeItem', function (e) {
            e.preventDefault();
            var tr = $(this).closest('tr');
            var row = tableOrderItemsShow.row(tr);
     
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

    //Tabla Order Patterns:
    if($('#tblOrderPatterns').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblOrderPatterns').DataTable({
            serverSide: true,
            /*processing: true,*/
            ajax: {
                "type": "GET",
                "url": "/api/admin/order-patterns",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', className: 'capitalize'},
                {data: 'patron'},
                {data: 'id', className: 'text-center'},
                {data: 'alta', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
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

    //Tabla Order Settings:
    if($('#tblOrderSettings').length > 0){
        $('#tblOrderSettings').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/order-settings",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', className: 'capitalize'},
                {data: 'alta', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
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
        var year = $('#myYear').val();
        var status = $('#myStatus').val() && $('#myStatus').val() != ''? $('#myStatus').val():false;
        var from = $('#myFrom').val() && $('#myFrom').val() != ''? $('#myFrom').val():'0';
        var to = $('#myTo').val() && $('#myTo').val() != ''? $('#myTo').val():'0';
        var accounting_on = $('#myModuleAccounting').val() == 1? true:false;
        var recordsTotal;       //Variable número de registros

        var orderbuy = $('#myOrderBuy').val() && $('#myOrderBuy').val() != '' ? $('#myOrderBuy').val():false;

        // Filters:

        // Load data from localstorage
        var jsonData = JSON.parse(localStorage.getItem('DataTables_tblOrders_'+window.location.pathname));

        $('#tblOrders thead tr').clone(true).appendTo('#tblOrders thead');
        $('#tblOrders thead tr:eq(1) th').each(function(i){
            var title = $(this).text(); 
            if(i == 13){
                $(this).html('');
            }else{
                // Check field on json of localstorage
                let value = jsonData && jsonData.columns[i].search.search ? jsonData.columns[i].search.search : '';
                let htmlRaw = `<input type="text" class="DTsearch" placeholder="${txtBuscar+' '+title}" value="${value}" />`
                $(this).html(htmlRaw);
                let element = $(this).html(htmlRaw);

                //Búsqueda por nombre y apellidos.
                if (i == 2) {
                    $('input', element).on('keyup change', function(){
                        // Diferenciamos si es nombre o apellido
                        let fullname = this.value.split(' ');

                        // Si es un solo nombre, se busca en el campo nombre dejando el campo apellido vacio
                        // caso contrario se busca en ambos campos
                        if(fullname.length == 1) {
                            // La busqueda se hace en el campo nombre
                            console.log(`El nombre es ${fullname[0]}`);
                            tableOrders
                                .column(2)
                                .search(fullname[0])
                                .column(3)
                                .search('')
                                .draw();
                        }else{
                            // La busqueda se hace en ambos campos (nombre y apellido)
                            console.log(`El nombre es ${fullname[0]} y el apellido es ${fullname[1]}`);
                            tableOrders
                                .column(2)
                                .search(fullname[0])
                                .column(3)
                                .search(fullname[1])
                                .draw();
                        }
                    });
                }
                // $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
                // aquí el código retirado
            }
            $('input', this).on('keyup change', function(){
                // Ignoramos la busqueda del campo de nombres
                if (i == 2) {
                    return;
                }

                if(tableOrders.column(i).search() !== this.value){
                    tableOrders
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });
        
        var tableOrders = $('#tblOrders').DataTable({
            order: [[ 0, "desc" ]], // Ordenar siempre por esta sección, no hacerlo desde el controlador.
            serverSide: true,
            processing: true,
            stateSave: true,
            // drawCallback: function(){
            //     var api = this.api(), data;
            //     var json = api.ajax.json();
            //     var currency = '€';

            //     var intVal = function (i) {
            //         return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
            //     };

            //     // Suma total todos los registros:
            //     var total = api
            //     .column(8)
            //     .data()
            //     .reduce(function (a, b) {
            //         return intVal(a) + intVal(b);
            //     }, 0);



            //     // var total = api.column(8, {search: 'applied'}).data().sum();
            //     $(api.column(8).footer()).html(
            //         total.toFixed(2)+currency
            //     );
            //     //     $('.select2-dt').select2();
            // },
            ajax: {
                "type": "GET",
                "url": `/api/admin/orders/${year}/${customer}/${provider}/${owner}/${status}/${from}/${to}/${orderbuy}`,
                /* "url": "/api/admin/orders/"+year+"/"+customer+"/"+provider+"/"+owner+"/"+status, */
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'created_at', name: 'orders.created_at', className: 'd-none' },     //Columna oculta
                {data: 'num', name: 'orders.order_num', className: 'text-right'},
                // {data: 'order_old_num', name: 'orders.order_old_num', className: 'text-right'},
                {data: 'manager', name: 'users.name'},
                {data: 'surname', name: 'users.surname', className: 'd-none' },     //Columna oculta
                {data: 'company', name: 'companies.name'},
                {data: 'budget_num', name: 'orders.budget_num', className: 'text-right'},
                {data: 'order_parent', name: 'orders.order_num', className: 'text-center'},
                {data: 'purchase_num', name: 'orders.purchase_num', className: 'text-right'},
                {data: 'name', name: 'orders.name'},
                {data: 'amount', className: 'text-right'},
                {data: 'fechaEntrega', className: 'text-right'},
                //{data: 'fechaEntregaPrevista', className: 'text-right'},
                {data: 'cost_center', name: 'cost_centers.name'},
                {data: 'estado', name: 'estado'},
                {data: 'acciones', orderable: false, searchable: false, className: 'text-right'}
            ],
            // Indicamos que columnas se tienen que ocultar
            columnDefs: [
                {
                    targets: 0,
                    visible: false,
                    searchable: false
                },
                {
                    targets: 3,
                    visible: false,
                    searchable: true
                }
            ],
            footerCallback: function(row, data, start, end, display){
                var api = this.api(), data;
                var json = tableOrders.ajax.json();
                var currency = '€';

                // Total de la columna importe:
                $(api.column(9).footer()).html(
                    `${json.total}${currency}`
                );

                // -- DATOS INSERVIBLES --
                // api.rows().every(function(){
                //     var data = this.data();
                // });

                //Separamos el importe de la moneda:
                // var intVal = function(i){
                //     return typeof i === 'string'? 
                //     i.replace(/[\'+currency+',]/g, '')*1 :
                //     typeof i === 'number' ?
                //         i : 0;
                // };

                //Total:
                // total = api
                //     .column(8, { search: 'applied' })
                //     .data()
                //     .reduce(function(a, b){
                //         return intVal(a) + intVal(b);
                //     }, 0);
                // $(api.column(8).footer()).html(
                //      total.toFixed(2)+currency   
                // );

                //$(api.column(8).footer()).html(json.amount);
            }
            // rowCallback: function( row, data, index ) {
            //     var api = this.api(), data;
            //     var sum = 0;
            //     sum += data.amount;
            //     $(api.column(8).footer()).html(sum);
            // }
        });

        //Suma importes:
        // var total_amount = tableOrders.column(8).data().sum();
        // $('#ordersAmount').text(total_amount);
    }

    //Tabla Orders Control Panel Purchases:
    if($('#tblOrdersControlPanelPurchases').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var year = $('#dtYear').val();
        var workplace = $('#dtWorkplace').val();
        var currency = $('#dtCurrency').val();

        //Filters:
        // Load data from localstorage
        var jsonData = JSON.parse(localStorage.getItem('DataTables_tblOrdersControlPanelPurchases_'+window.location.pathname));

        $('#tblOrdersControlPanelPurchases thead tr').clone(true).appendTo('#tblOrdersControlPanelPurchases thead');
        $('#tblOrdersControlPanelPurchases thead tr:eq(1) th').each(function(i){
            var title = $(this).text(); 

            if(i == 12){
                $(this).html('');
            }else{
                // Check field on json of localstorage
                let value = jsonData && jsonData.columns[i].search.search ? jsonData.columns[i].search.search : '';
                let htmlRaw = `<input type="text" class="DTsearch" placeholder="${txtBuscar+' '+title}" value="${value}" />`
                $(this).html(htmlRaw);
                let element = $(this).html(htmlRaw);

                //Búsqueda por nombre y apellidos.
                if (i == 5) {
                    $('input', element).on('keyup change', function(){
                        // Diferenciamos si es nombre o apellido
                        let fullname = this.value.split(' ');

                        // Si es un solo nombre, se busca en el campo nombre dejando el campo apellido vacio
                        // caso contrario se busca en ambos campos
                        if(fullname.length == 1) {
                            // La busqueda se hace en el campo nombre
                            tableOrdersCPPurchases
                                .column(5)
                                .search(fullname[0])
                                .column(6)
                                .search('')
                                .draw();
                        }else{
                            // La busqueda se hace en ambos campos (nombre y apellido)
                            tableOrdersCPPurchases
                                .column(5)
                                .search(fullname[0])
                                .column(6)
                                .search(fullname[1])
                                .draw();
                        }
                    });
                }
            }
         
            $('input', this).on('keyup change', function(){
                if(tableOrdersCPPurchases.column(i).search() !== this.value){
                    tableOrdersCPPurchases
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });

        var tableOrdersCPPurchases = $('#tblOrdersControlPanelPurchases').DataTable({
            order: [[ 0, "desc" ]],
            serverSide: false,  //01/09/22: en false para permitir la suma de totales del footer.
            processing: true,
            autoWidth: false,
            // drawCallback: function(){
            //     $('.select2-dt').select2();
            // },
            ajax: {
                "type": "GET",
                "url": "/api/admin/orders-control-panel-purchases/"+year,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'fecha', name: 'order_previsions.month'},
                {data: 'provider', name: 'companies.name'},
                //{data: 'business_area', name: 'business_areas.name'},
                {data: 'order_num', name: 'orders.order_num', className: 'text-right'},
                {data: 'purchase_num', name: 'orders.purchase_num', className: 'text-right'},
                {data: 'name', name: 'companies.name'},
                {data: 'manager', name: 'users.name'},
                {data: 'surname', name: 'users.surname', className: 'd-none' },
                {data: 'workplace', name: 'workplaces.name'},
                {data: 'amount', name: 'amount', className: 'text-right'},
                {data: 'a_facturar', name: 'a_facturar', className: 'text-right'},
                {data: 'invoice_amount', name: 'invoice_amount', className: 'text-right'},
                {data: 'estado', name: 'estado'},
                {data: 'acciones', name: 'action', orderable: false, searchable: false, className: 'text-right'}
            ],
            "footerCallback": function(row, data, start, end, display){
                var api = this.api(), data;
                var json = api.ajax.json();

                var currency = false;
                api.rows().every(function(){
                    var data = this.data();
                });

                if(!currency) currency = '€';

                //Separamos el importe de la moneda:
                var intVal = function(i){
                    return typeof i === 'string'? 
                    i.replace(/[\'+currency+',]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
                };

                // Total over all pages
                // total = api
                // .column(8)
                // .data()
                // .reduce(function(a, b){
                //     return intVal(a) + intVal(b);
                // }, 0);

                //Total:
                total = api
                .column(8, { search: 'applied' })
                .data()
                .reduce(function(a, b){
                    return intVal(a) + intVal(b);
                }, 0);

                $(api.column(8).footer()).html(
                    total.toFixed(2)+currency   
                );

                //Total por filtro aplicado:
                a_facturar = api
                .column(9, { search: 'applied' })
                .data()
                .reduce(function(a, b){
                    return intVal(a) + intVal(b);
                }, 0);

                $(api.column(9).footer()).html(
                    a_facturar.toFixed(2)+currency    
                );
            }
        });
    }

    //Tabla Orders Control Panel Sales:
    if($('#tblOrdersControlPanelSales').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var year = $('#dtYear').val();
        var workplace = $('#dtWorkplace').val();
        var currency = $('#dtCurrency').val();

        //Filters:
        // Load data from localstorage
        var jsonData = JSON.parse(localStorage.getItem('DataTables_tblOrdersControlPanelSales_'+window.location.pathname));

        $('#tblOrdersControlPanelSales thead tr').clone(true).appendTo('#tblOrdersControlPanelSales thead');
        $('#tblOrdersControlPanelSales thead tr:eq(1) th').each(function(i){
            var title = $(this).text(); 

            if(i == 12){
                $(this).html('');
            }else{
                // Check field on json of localstorage
                let value = jsonData && jsonData.columns[i].search.search ? jsonData.columns[i].search.search : '';
                let htmlRaw = `<input type="text" class="DTsearch" placeholder="${txtBuscar+' '+title}" value="${value}" />`
                $(this).html(htmlRaw);
                let element = $(this).html(htmlRaw);

                //Búsqueda por nombre y apellidos.
                if (i == 5) {
                    $('input', element).on('keyup change', function(){
                        // Diferenciamos si es nombre o apellido
                        let fullname = this.value.split(' ');

                        // Si es un solo nombre, se busca en el campo nombre dejando el campo apellido vacio
                        // caso contrario se busca en ambos campos
                        if(fullname.length == 1) {
                            // La busqueda se hace en el campo nombre
                            tableOrdersCPSales
                                .column(5)
                                .search(fullname[0])
                                .column(6)
                                .search('')
                                .draw();
                        }else{
                            // La busqueda se hace en ambos campos (nombre y apellido)
                            tableOrdersCPSales
                                .column(5)
                                .search(fullname[0])
                                .column(6)
                                .search(fullname[1])
                                .draw();
                        }
                    });
                }
            }
         
            $('input', this).on('keyup change', function(){
                if(tableOrdersCPSales.column(i).search() !== this.value){
                    tableOrdersCPSales
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });
        
        var tableOrdersCPSales = $('#tblOrdersControlPanelSales').DataTable({
            order: [[ 0, "desc" ]],
            serverSide: false,  //01/09/22: en false para permitir la suma de totales del footer.
            processing: true,
            autoWidth: false,
            // drawCallback: function(){
            //     $('.select2-dt').select2();
            // },
            ajax: {
                "type": "GET",
                "url": "/api/admin/orders-control-panel-sales/"+year+'/'+workplace,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'fecha', name: 'order_previsions.month'},
                {data: 'customer', name: 'companies.name'},
                //{data: 'business_area', name: 'business_areas.name'},
                {data: 'order_num', name: 'orders.order_num', className: 'text-right'},
                {data: 'purchase_num', name: 'orders.purchase_num', className: 'text-right'},
                {data: 'name', name: 'companies.name'},
                {data: 'manager', name: 'users.name'},
                {data: 'surname', name: 'users.surname', className: 'd-none' },
                {data: 'workplace', name: 'workplaces.name'},
                // {data: 'finalCustomer', name: 'companies.name'},
                {data: 'amount', name: 'amount', className: 'text-right'},
                {data: 'a_facturar', name: 'a_facturar', className: 'text-right'},
                {data: 'invoice_amount', name: 'invoice_amount', className: 'text-right'},
                {data: 'estado', name: 'estado'},
                {data: 'acciones', name: 'action', orderable: false, searchable: false, className: 'text-right'}
            ],
            "footerCallback": function(row, data, start, end, display){
                var api = this.api(), data;
                var json = api.ajax.json();

                var currency = false;
                api.rows().every(function(){
                    var data = this.data();
                });

                if(!currency) currency = '€';

                //Separamos el importe de la moneda:
                var intVal = function(i){
                    return typeof i === 'string'? 
                    i.replace(/[\'+currency+',]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
                };

                // Total over all pages
                // total = api
                // .column(8)
                // .data()
                // .reduce(function(a, b){
                //     return intVal(a) + intVal(b);
                // }, 0);

                //Total:
                total = api
                .column(8, { search: 'applied' })
                .data()
                .reduce(function(a, b){
                    return intVal(a) + intVal(b);
                }, 0);

                $(api.column(8).footer()).html(
                    total.toFixed(2)+currency   
                );

                //Total por filtro aplicado:
                a_facturar = api
                .column(9, { search: 'applied' })
                .data()
                .reduce(function(a, b){
                    return intVal(a) + intVal(b);
                }, 0);

                $(api.column(9).footer()).html(
                    a_facturar.toFixed(2)+currency    
                );
            }
        });
    }

    //Tabla Payment Methods:
    if($('#tblPaymentMethods').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');   
        
        var tablePaymentMethods = $('#tblPaymentMethods').DataTable({
            serverSide: true,
            processing: true, 
            ajax: {
                "type": "GET",
                "url": "/api/admin/payment-methods",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'description'},
                {data: 'acciones', className: 'text-right'}
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

    //Tabla Procedure Patterns:
    if($('#tblProcedurePatterns').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblProcedurePatterns').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/procedure-patterns",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', className: 'capitalize'},
                {data: 'patron'},
                {data: 'alta', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
                {"targets": -1,
                "render": function(data, type, row){
                    //Estado:
                    if(row['editar']){
                        var checked = (row['status'] == 1)? 'checked':'';
                        estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+' ><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="procedure-patterns/status"></span>';
                        estado += '</label>';
                    }

                    //Editar:
                    var editar = '';
                    if(row['editar']){
                        editar = '<a href="procedure-patterns/'+row['id']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['eliminar']){
                        eliminar = '<form method="POST" action="procedure-patterns/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtPatronProcedimientoConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Procedures:
    if($('#tblProcedures').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        $('#tblProcedures').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/procedures",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'procedure_num', className: 'capitalize'},
                {data: 'name'},
                {data: 'alta', className: 'text-right'},
                {data: 'last_revision', className: 'text-right'},
                {data: 'id'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
                {"targets": 4,
                "render": function(data, type, row){
                    return row['nombre']+' '+row['surname'];
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    //Estado:
                    var estado = '';
                    // if(row['editar']){
                    //     var checked = (row['status'] == 1)? 'checked':'';
                    //     estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtOnOff+'">';
                    //         estado += '<input type="checkbox" name="status" '+checked+' ><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="procedures/status"></span>';
                    //     estado += '</label>';
                    // }

                    //Imprimir:
                    var print = '<a target="_blank" href="/admin/procedure-revisions/'+row['revisionId']+'/print" class="btn btn-xs btn-info"><i class="fa fa-print" rel="tooltip" title="'+txtDocumentoVer+'"></i></a>';

                    //Editar:
                    var editar = '';
                    if(row['editar']){
                        editar = '<a href="procedures/'+row['id']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['eliminar']){
                        eliminar = '<form method="POST" action="procedures/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtProcedimientoConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+print+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Product Attributes:
    if($('#tblProductAttributes').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var product_id = $('#productId').val();

        $('#tblProductAttributes').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/product-attributes/"+product_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'datatype'},
                {data: 'value'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }

    //Tabla Product Categories:
    if($('#tblProductCategories').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var product_id = $('#productId').val();

        $('#tblProductCategories').DataTable({
            order: [[ 2, "desc" ]],
            serverSide: false,
            processing: false,
            ajax: {
                "type": "GET",
                "url": "/api/admin/product-categories/"+product_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'level'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }

    //Tabla Product Patterns:
    if($('#tblProductPatterns').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblProductPatterns').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/product-patterns",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', className: 'capitalize'},
                {data: 'patron'},
                {data: 'alta', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
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

    //Tabla Product Providers:
    if($('#tblProductProviders').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var product_id = $('#productId').val();
        
        $('#tblProductProviders').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/product-providers/"+product_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', name: 'companies.name', className: 'capitalize'},
                {data: 'product', name: 'products.name'},
                {data: 'ref', name: 'products.ref'},
                {data: 'price', className: 'text-right'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }

    //Tabla Product Providers Correspondences:
    if($('#tblProductProvidersCorrespondences').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var product_id = $('#productId').val();
        var provider_id = $('#providerId').val();
        
        $('#tblProductProvidersCorrespondences').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/product-providers-correspondences/"+product_id+'/'+provider_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', name: 'products.name'},
                {data: 'ref', name: 'products.ref'},
                {data: 'image', className: 'text-center', orderable: false, searchable: false},
                {data: 'price', name: 'price', className: 'text-right'},
                {data: 'acciones', className: 'text-right', orderable: false, searchable: false}
            ]
        });
    }

    //Tabla Product Serial Attributes:
    if($('#tblProductSerialAttributes').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var serial_id = $('#serialId').val();

        //Filters:
        $('#tblProductSerialAttributes thead tr').clone(true).appendTo('#tblProductSerialAttributes thead');
        $('#tblProductSerialAttributes thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
     
            $('input', this).on('keyup change', function(){
                if(tableProductSerialAttributes.column(i).search() !== this.value){
                    tableProductSerialAttributes
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });
        
        var tableProductSerialAttributes = $('#tblProductSerialAttributes').DataTable({
            serverSide: false,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/product-serials-cards/"+serial_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'datatype'},
                {data: 'description'},
                {data: 'value'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }

    //Tabla Product Serials:
    if($('#tblProductSerials').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var product_id = $('#productId').val();

        $('#tblProductSerials').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/product-serials/"+product_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'serial'},
                {data: 'alta', className: 'text-right'},
                {data: 'employee'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }

    //Tabla Product Stock Movements:
    if($('#tblProductStockMovements').length > 0){
        function format(d){
            // `d` is the original data object for the row
            var obvs = d.observations? d.observations:txtObservacionesSin;
            return '<table cellpadding="0" cellspacing="0" border="0">'+
                '<tr>'+
                    '<td colspan="9" class="no-border">'+obvs+'</td>'+
                '</tr>'+
            '</table>';
        }

        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var product_id = $('#productId').val();

        //Filters:
        // Load data from localstorage
        var jsonData = JSON.parse(localStorage.getItem('DataTables_tblProductStockMovements_'+window.location.pathname));

        $('#tblProductStockMovements thead tr').clone(true).appendTo('#tblProductStockMovements thead');
        $('#tblProductStockMovements thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 0 || i == 4 || i == 5 || i == 6 || i == 8){
                $(this).html('');
            }else{

                // Check field on json of localstorage
                let value = jsonData && jsonData.columns[i].search.search ? jsonData.columns[i].search.search : '';
                let htmlRaw = `<input type="text" class="DTsearch" placeholder="${txtBuscar+' '+title}" value="${value}" />`
                $(this).html(htmlRaw);

                // $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
            }
     
            $('input', this).on('keyup change', function(){
                if(tableProductStockMovements.column(i).search() !== this.value){
                    tableProductStockMovements
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });

        var tableProductStockMovements = $('#tblProductStockMovements').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/product-stock-movements/"+product_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'fecha', className: 'text-right'},
                {data: 'movement', name: 'stock_movements.name'},
                {data: 'store', name: 'stores.name'},
                {data: 'batch', name: 'batches.name'},
                {data: 'qty', className: 'text-right'},
                {data: 'stock', className: 'text-right'},
                {data: 'doc', className: 'text-center'},
                {data: 'user', name: 'users.name'},
                {data: 'acciones', className: 'text-right'}
            ]
        });

        $('#tblProductStockMovements').on('click', 'td .btnSeeItem', function (e) {
            e.preventDefault();
            var tr = $(this).closest('tr');
            var row = tableProductStockMovements.row(tr);
     
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

    //Tabla Product Units:
    if($('#tblProductUnits').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var product_id = $('#productId').val();
        
        $('#tblProductUnits').DataTable({
            "order": [[ 2, "desc" ]],
            serverSide: false,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/product-units/"+product_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'unit', className: 'capitalize'},
                {data: 'symbol'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
                {"targets": -1,
                "render": function(data, type, row){
                    //Estado:
                    var estado = '<span style="display: none">'+row['selected']+'</span>';
                    if(row['editar']){
                        var checked = row['selected'] == 1? 'checked':'';
                        estado += '<label class="custom-chk-rdb btnSetProductUnit" rel="tooltip" title="'+txtUnidadAdoptar+'">';
                            estado += '<input type="checkbox" name="status" '+checked+' ><span class="chk chk-product-unit" data-productid="'+product_id+'" data-unitid="'+row['id']+'"></span>';
                        estado += '</label>';
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
        var company = $('#myCompany').val();

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
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/products/"+company,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'producto'},
                {data: 'descripcion'},
                {data: 'price', className: 'text-right'},
                {data: 'id', className: 'text-center'},
                {data: 'id', className: 'text-center'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
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

    //Tabla Project Expenses:
    if($('#tblProjectExpenses').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var project_id = $('#myProject').val();

        $('#tblProjectExpenses').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/project-expenses/"+project_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'concept'},
                {data: 'expensed_in', className: 'text-right'},
                {data: 'amount', className: 'text-right'},
                {data: 'type'},
                {data: 'provider'},
                {data: 'doc', className: 'text-center'},
                {data: 'acciones', className: 'text-right'}
            ],
            "footerCallback": function(row, data, start, end, display){
                var api = this.api(), data;

                var currency = false;
                var refund = false;
                api.rows().every(function(){
                    var data = this.data();
                    currency = '€';
                });
 
                // Remove the formatting to get integer data for summation
                var intVal = function(i){
                    //console.log(i);
                    return typeof i === 'string'? 
                    i.replace(/[\'+currency+',]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
                };
 
                //Total por filtro aplicado:
                total_amount = api
                .column(2, { search: 'applied' })
                .data()
                .reduce(function(a, b){
                    return intVal(a) + intVal(b);
                }, 0);

                $(api.column(2).footer()).html(
                    total_amount.toFixed(2)+currency    
                );
            }
        });
    }

    //Tabla Project Patterns:
    if($('#tblProjectPatterns').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblProjectPatterns').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/project-patterns",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', className: 'capitalize'},
                {data: 'patron'},
                {data: 'alta', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
                {"targets": -1,
                "render": function(data, type, row){
                    //Estado:
                    if(row['editar']){
                        var checked = (row['status'] == 1)? 'checked':'';
                        estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtOnOff+'">';
                            estado += '<input type="checkbox" name="status" '+checked+' ><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="project-patterns/status"></span>';
                        estado += '</label>';
                    }

                    //Editar:
                    var editar = '';
                    if(row['editar']){
                        editar = '<a href="project-patterns/'+row['id']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['eliminar']){
                        eliminar = '<form method="POST" action="project-patterns/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtPatronProyectoConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+editar+' '+eliminar;
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
        var jsonData = JSON.parse(localStorage.getItem('DataTables_tblProjects_'+window.location.pathname));

        $('#tblProjects thead tr').clone(true).appendTo('#tblProjects thead');
        $('#tblProjects thead tr:eq(1) th').each(function(i){
            var title = $(this).text(); 
            if(i == 5 || i == 6 || i == 7 || i == 9){
                $(this).html('');
            }else{
                let value = jsonData && jsonData.columns[i].search.search ? jsonData.columns[i].search.search : '';
                let htmlRaw = `<input type="text" class="DTsearch" placeholder="${txtBuscar+' '+title}" value="${value}" />`
                $(this).html(htmlRaw);

                // $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
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
            serverSide: true,
            processing: true,
            stateSave: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/projects/"+status,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', name: 'projects.name'},
                {data: 'customer', name: 'companies.name'},
                {data: 'alta', className: 'text-right'},
                {data: 'entrega_prevista', className: 'text-right'},
                {data: 'entrega', className: 'text-right'},
                {data: 'hours_forecast', className: 'text-right'},
                {data: 'hours_worked', visible: false},
                {data: 'progress', className: 'position-relative'},
                {data: 'estado', name: 'estado'},
                {data: 'acciones', orderable: false, searchable: false, className: 'text-right'}
            ],
            "footerCallback": function(row, data, start, end, display){
                var api = this.api(), data;

                var intVal = function(i){
                    return i;
                };

                //Total por filtro aplicado:
                hours_forecast = api
                .column(5, { search: 'applied' })
                .data()
                .reduce(function(a, b){
                    return intVal(a) + intVal(b);
                }, 0);

                //Horas trabajadas:
                hours_worked = api
                .column(6, { search: 'applied' })
                .data()
                .reduce(function(a, b){
                    return intVal(a) + intVal(b);
                }, 0);

                var pending = hours_forecast - hours_worked;

                $(api.column(5).footer()).html(
                    hours_forecast.toFixed(2)+' / '+pending.toFixed(2)
                );
            }
        });
    }

    //Tabla Protocol Items:
    if($('#tblProtocolItems').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var protocol_id = $('#dtProtocolId').val();
        var collection = [];

        var tableProtocolItems = $('#tblProtocolItems').DataTable({
            serverSide: true,
            processing: true,
            stateSave: true,
            retrieve: true,
            rowReorder: {
                rowOrder: true,
                dataSrc: 'sort',
                selector: 'tr'
            },
            ajax: {
                "type": "GET",
                "url": "/api/admin/protocol-items/"+protocol_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'title', name: 'protocol_items.title'},
                {data: 'created_at', className: 'text-right'},
                {data: 'author', name: 'users.name'},
                {data: 'sort', className: 'text-center'},
                {data: 'estado', name: 'estado'},
                {data: 'acciones', orderable: false, searchable: false, className: 'text-right'}
            ]
        });

        //Reordenar filas:
        tableProtocolItems.on('row-reordered', function (e, diff, edit){
            for(var i=0; i<diff.length; i++){
                var rowData = tableProtocolItems.row(diff[i].node).data();
                collection.push({
                    id: rowData['id'],
                    pos: diff[i].newData    
                });

                $.ajax({
                    url: "/admin/protocol-items/reorder-items", 
                    type: "POST",
                    data: {
                        _token: tokenDT,
                        collection: collection
                    }, 
                    cache: false,
                    success: function(response){
                        collection = [];
                    }
                });
            }
        });
    }

    //Tabla Protocol Users:
    if($('#tblProtocolUsers').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var protocol_id = $('#dtProtocolId').val();

        var tableProtocolUsers = $('#tblProtocolUsers').DataTable({
            serverSide: true,
            processing: true,
            stateSave: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/protocol-users/"+protocol_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'user', name: 'users.name'},
                {data: 'created_at', className: 'text-right'},
                {data: 'signed_at', className: 'text-right'},
                {data: 'acciones', orderable: false, searchable: false, className: 'text-right'}
            ]
        });
    }

    //Tabla Protocols:
    if($('#tblProtocols').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        var tableProtocols = $('#tblProtocols').DataTable({
            serverSide: true,
            processing: true,
            stateSave: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/protocols",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', name: 'protocols.name'},
                {data: 'created_at', className: 'text-right'},
                {data: 'author'},
                {data: 'estado', name: 'estado'},
                {data: 'acciones', orderable: false, searchable: false, className: 'text-right'}
            ]
        });
    }

    //Tabla Provider Products:
    if($('#tblProviderProducts').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var provider = $('#dtProvider').val();

        //Filters:
        $('#tblProviderProducts thead tr').clone(true).appendTo('#tblProviderProducts thead');
        $('#tblProviderProducts thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 3 || i == 5){
                $(this).html('');
            }else{
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
            }
     
            $('input', this).on('keyup change', function(){
                if(tableProviderProducts.column(i).search() !== this.value){
                    tableProviderProducts
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });
        
        var tableProviderProducts = $('#tblProviderProducts').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/provider-products/"+provider,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'descripcion'},
                {data: 'internal_ref'},
                {data: 'image', className: 'text-center', orderable: false, searchable: false},
                {data: 'price', className: 'text-right'},
                {data: 'acciones', className: 'text-right', orderable: false, searchable: false}
            ]
        });
    }

    //Tabla Providers:
    if($('#tblProviders').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        //Filters:
        
        // Load data from localstorage
        var jsonData = JSON.parse(localStorage.getItem('DataTables_tblProviders_'+window.location.pathname));

        $('#tblProviders thead tr').clone(true).appendTo('#tblProviders thead');
        $('#tblProviders thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 3){
                $(this).html('');
            }else{

                // Check field on json of localstorage
                let value = jsonData && jsonData.columns[i].search.search ? jsonData.columns[i].search.search : '';
                let htmlRaw = `<input type="text" class="DTsearch" placeholder="${txtBuscar+' '+title}" value="${value}" />`
                $(this).html(htmlRaw);

                // $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
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
            serverSide: true,
            processing: true,
            stateSave: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/providers",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', name: 'companies.name'},
                {data: 'tradename', name: 'companies.tradename'},
                {data: 'nif', name: 'companies.nif'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }

    //Tabla Provinces:
    if($('#tblProvinces').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var country_id = $('#countryId').val();
        
        $('#tblProvinces').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/provinces/"+country_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
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
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/purchase-patterns",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', className: 'capitalize'},
                {data: 'patron'},
                {data: 'id', className: 'text-center'},
                {data: 'alta', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
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
            serverSide: true,
            /*processing: true,*/
            ajax: {
                "type": "GET",
                "url": "/api/admin/question-answers/"+question_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'value', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
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
        var quiz_id = $('#quizId').val();
        
        $('#tblQuestions').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/questions/"+quiz_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'question'},
                {data: 'type'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }

    //Tabla Quiz Edition Questions:
    if($('#tblQuizEditionQuestions').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var edition_id = $('#editionId').val();
        var disabled = $('#editionDisabled').val();
        
        $('#tblQuizEditionQuestions').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/quiz-edition-questions/"+edition_id+"/"+disabled,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'sort', className: 'text-right'},
                {data: 'name'},
                {data: 'question'},
                {data: 'type'},
                {data: 'points', className: 'text-right'},
                {data: 'acciones', className: 'text-right'}
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
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/quiz-editions",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'quiz'},
                {data: 'alta', className: 'text-right'},
                {data: 'id', className: 'text-center'},
                {data: 'start_', className: 'text-right'},
                {data: 'finish_', className: 'text-right'},
                {data: 'time', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
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
        
        var tableQuizzes = $('#tblQuizzes').DataTable({
            serverSide: true,
            processing: true,
            stateSave: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/quizzes",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', name: 'quizzes.name'},
                {data: 'alta', className: 'text-right'},
                {data: 'description', name: 'quizzes.description'},
                {data: 'owner'},
                {data: 'acciones', className: 'text-right'}
            ]
            /*columnDefs: [
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
            ] */
        });
    }

    //Tabla Receivings:
    if($('#tblReceivings').length > 0){
        function format(d){
            // `d` is the original data object for the row
            var obvs = d.observations? d.observations:txtObservacionesSin;
            return '<table cellpadding="0" cellspacing="0" border="0">'+
                '<tr>'+
                    '<td colspan="5" class="no-border">'+obvs+'</td>'+
                '</tr>'+
            '</table>';
        }

        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        var tableReceivings = $('#tblReceivings').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/receivings",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'estimated_reception_date', name: 'deliveries.estimated_reception_date', className: 'text-right'},
                {data: 'provider', name: 'companies.name'},
                {data: 'orderNum', name: 'orders.purchase_num'},
                {data: 'deliveryNum', name: 'deliveries.delivery_num'},
                {data: 'acciones', className: 'text-right'}
            ]
        });

        $('#tblReceivings tbody').on('click', 'td .btnSeeItem', function (e) {
            e.preventDefault();
            var tr = $(this).closest('tr');
            var row = tableReceivings.row(tr);
     
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

    //Tabla Roles:
    if($('#tblRoles').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblRoles').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/roles",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'description'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }

    //Tabla SAT:
    if($('#tblSats').length > 0){
        function format(d){
            // `d` is the original data object for the row
            var obvs = d.description? d.description:txtObservacionesSin;
            return '<table cellpadding="0" cellspacing="0" border="0">'+
                '<tr>'+
                    '<td colspan="8" class="no-border">'+obvs+'</td>'+
                '</tr>'+
            '</table>';
        }

        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        var tableSats =  $('#tblSats').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/sats",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'num', className: 'text-right'},
                {data: 'customer'},
                {data: 'provider'},
                {data: 'alta', className: 'text-right'},
                {data: 'delivery_date', className: 'text-right'},
                {data: 'amount', className: 'text-right'},
                {data: 'estado', className: 'text-right'},
                {data: 'acciones', className: 'text-right'}
            ]
        });

        $('#tblSats tbody').on('click', 'td .btnSeeItem', function (e) {
            e.preventDefault();
            var tr = $(this).closest('tr');
            var row = tableSats.row(tr);
     
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

    //Tabla SAT Patterns:
    if($('#tblSatPatterns').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblSatPatterns').DataTable({
            serverSide: true,
            /*processing: true,*/
            ajax: {
                "type": "GET",
                "url": "/api/admin/sat-patterns",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', className: 'capitalize'},
                {data: 'patron'},
                {data: 'id', className: 'text-center'},
                {data: 'alta', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
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
                            estado += '<input type="checkbox" name="status" '+checked+'><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="sat-patterns/status"></span>';
                        estado += '</label>';

                        //Editar:
                        editar = '<a href="sat-patterns/'+row['id']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['eliminar']){
                        eliminar = '<form method="POST" action="sat-patterns/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtPatronSatConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return estado+' '+editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Schedule Event Types:
    if($('#tblScheduleEventTypes').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblScheduleEventTypes').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/schedule-event-types",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'event'},
                {data: 'description'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }

    //Tabla Shipment Patterns:
    if($('#tblShipmentPatterns').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblShipmentPatterns').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/shipment-patterns",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', className: 'capitalize'},
                {data: 'patron'},
                {data: 'id', className: 'text-center'},
                {data: 'alta', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
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

    //Tabla Staff Pick Candidates:
    if($('#tblStaffPickCandidates').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var process_id = $('#processId').val();
        
        $('#tblStaffPickCandidates').DataTable({
            serverSide: true,
            /*processing: true,*/
            ajax: {
                "type": "GET",
                "url": "/api/admin/staff-pick-candidates/"+process_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', className: 'capitalize'},
                {data: 'id', className: 'text-center'},
                {data: 'alta', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
                {"targets": 0,
                "render": function(data, type, row){
                    return row['name']+' '+row['surname'];
                }
                },
                {"targets": 1,
                "render": function(data, type, row){
                    var stars = '<div class="rating">';
                    for(i=1; i<=5; i++){
                        var active = i <= row['assessment']? 'active':'';
                        stars += '<i class="fa fa-star '+active+'"></i>';
                    }  
                    stars += '</div>';  
                    return stars;
                }
                },
                {"targets": -1,
                "render": function(data, type, row){
                    var editar = '';
                    if(row['editar']){
                        //Editar:
                        editar = '<a href="/admin/staff-pick-candidates/'+row['id']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
                    }

                    //Eliminar:
                    var eliminar = '';
                    if(row['editar']){
                        eliminar = '<form method="POST" action="/admin/staff-pick-candidates/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
                            eliminar += '<input name="_method" type="hidden" value="DELETE">';
                            eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
                            eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtCandidatoConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
                        eliminar += '</form>';
                    }

                    return editar+' '+eliminar;
                }
                }
            ]
        });
    }

    //Tabla Staff Picks:
    if($('#tblStaffPicks').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblStaffPicks').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/staff-picks",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', className: 'capitalize'},
                {data: 'published_at', className: 'text-right'},
                {data: 'published_end', className: 'text-right'},
                {data: 'candidates', className: 'text-right'},
                {data: 'acciones', className: 'text-right'}
            ]
            // columnDefs: [
            //     {"targets": 0,
            //     "render": function(data, type, row){
            //         if(row['name']){
            //             return row['name'];
            //         }else{
            //             return row['id'];
            //         }
            //     }
            //     },
            //     {"targets": -1,
            //     "render": function(data, type, row){
            //         var estado = '';
            //         var editar = '';
            //         if(row['editar']){
            //             //Estado:
            //             var checked = (row['status'] == 1)? 'checked':'';
            //             var estado = '<label class="custom-chk-rdb" rel="tooltip" title="'+txtProcesoOnOff+'">';
            //                 estado += '<input type="checkbox" name="status" '+checked+'><span class="chk chk-action" data-id="'+row['id']+'" data-ctrl="staff-pick/status"></span>';
            //             estado += '</label>';

            //             //Editar:
            //             editar = '<a href="staff-pick/'+row['id']+'/edit" class="btn btn-xs btn-info" rel="tooltip" title="'+txtEditar+'"><i class="fa fa-edit"></i></a>';
            //         }

            //         //Eliminar:
            //         var eliminar = '';
            //         if(row['eliminar']){
            //             eliminar = '<form method="POST" action="staff-pick/'+row['id']+'" class="frm-inline" role="form" accept-charset="UTF-8">';
            //                 eliminar += '<input name="_method" type="hidden" value="DELETE">';
            //                 eliminar += '<input type="hidden" name="_token" value="'+tokenDT+'">';
            //                 eliminar += '<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtProcesoConfirmEliminar+'"><i class="fa fa-trash"></i></button>';
            //             eliminar += '</form>';
            //         }

            //         return estado+' '+editar+' '+eliminar;
            //     }
            //     }
            // ]
        });
    }

    //Tabla Stock Movements:
    if($('#tblStockMovements').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblStockMovements').DataTable({
            serverSide: false,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/stock-movements",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'acronym', className: 'text-center'},
                {data: 'sign'},
                {data: 'explicacion'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }

    //Tabla Stores:
    if($('#tblStores').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblStores').DataTable({
            serverSide: false,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/stores",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'tipo'},
                {data: 'alta', className: 'text-right'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }

    //Tabla Teams:
    if($('#tblTeams').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        $('#tblTeams').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/teams",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'id', className: 'text-right'},
                {data: 'id', className: 'text-center'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
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
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/timetable-items",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
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
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }

    //Tabla Towns:
    if($('#tblTowns').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var province_id = $('#provinceId').val();
        
        $('#tblTowns').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/towns/"+province_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
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
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/units/",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'symbol'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
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
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/user-categories",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
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
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/user-companies/"+user,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
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
            serverSide: false,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/user-timetables/"+from+"/"+to+"/"+user,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'fecha', className: 'text-right'},
                {data: 'hour', className: 'text-right'},
                {data: 'name'},
                {data: 'accumulated', className: 'text-right'},
                {data: 'comments'},
                {data: 'acciones', name: 'acciones', className: 'text-right'}
            ],
            "footerCallback": function(row, data, start, end, display){
                var api = this.api(), data;
                if(api.rows().count() > 0){

                    var horasTrabajadas = 0;
                    var hoursHT = 0;

                    var horasATrabajar = 0;
                    var hoursAT = 0;

                    var horasDiferencia = 0;
                    var hoursD = 0;

                    api.rows().every(function(){
                        var data = this.data();

                        //Horas trabajadas:
                        if(data.accumulated != '' && data.accumulated_value > '0'){
                            var pHT = data.accumulated_value? data.accumulated_value.split(':'):[0,0];
                            var minutesHT = parseInt(pHT[0]) * 60;
                            minutesHT = parseInt(minutesHT) + parseInt(pHT[1]);
                            var secondsHT = parseInt(minutesHT) * 60;
                            hoursHT = parseInt(hoursHT) + parseInt(secondsHT);    
                        }

                        //Horas a trabajar:
                        if(data.hoursOfWork != '' && data.hoursOfWork != '0'){
                            var pAT = data.hoursOfWork.split(':');
                            var minutesAT = parseInt(pAT[0]) * 60;
                            minutesAT = parseInt(minutesAT) + parseInt(pAT[1]);
                            var secondsAT = parseInt(minutesAT) * 60;
                            hoursAT = parseInt(hoursAT) + parseInt(secondsAT); 
                        }
                    });

                    //Diferencia de horas:
                    hoursD = hoursHT - hoursAT;
                    hoursD = Math.abs(hoursD);
                    var classDif = hoursHT >= hoursAT? 'text-success':'text-danger';
                    horasDiferencia = secondsToString(hoursD);

                    //Convertimos segundos a hora con minutos:
                    horasTrabajadas = secondsToString(hoursHT);
                    horasATrabajar = secondsToString(hoursAT);

                    $(api.column(3).footer()).html(
                        horasTrabajadas+' / '+horasATrabajar+' <strong class="'+classDif+' ml-2">'+horasDiferencia+'</strong>'    
                    );
                }
            }
        });
    }

    //Tabla Users:
    if($('#tblUsers').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var all = $('#all').val();
        var method = all? '/all':'';

        //Filters:
        // Load data from localstorage
        var jsonData = JSON.parse(localStorage.getItem('DataTables_tblUsers_'+window.location.pathname));

        $('#tblUsers thead tr').clone(true).appendTo('#tblUsers thead');
        $('#tblUsers thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 6 || i == 7){
                $(this).html('');
            }else{
                // Check field on json of localstorage
                let value = jsonData && jsonData.columns[i].search.search ? jsonData.columns[i].search.search : '';
                let htmlRaw = `<input type="text" class="DTsearch" placeholder="${txtBuscar+' '+title}" value="${value}" />`
                $(this).html(htmlRaw);
                let element = $(this).html(htmlRaw);

                //Búsqueda por nombre y apellidos.
                if (i == 0) {
                    $('input', element).on('keyup change', function(){
                        // Diferenciamos si es nombre o apellido
                        let fullname = this.value.split(' ');

                        // Si es un solo nombre, se busca en el campo nombre dejando el campo apellido vacio
                        // caso contrario se busca en ambos campos
                        if(fullname.length == 1) {
                            // La busqueda se hace en el campo nombre
                            tableUsers
                                .column(0)
                                .search(fullname[0])
                                .column(1)
                                .search('')
                                .draw();
                        }else{
                            // La busqueda se hace en ambos campos (nombre y apellido)
                            tableUsers
                                .column(0)
                                .search(fullname[0])
                                .column(1)
                                .search(fullname[1])
                                .draw();
                        }
                    });
                }
            }

            $('input', this).on('keyup change', function(){
                // Ignoramos la busqueda del campo de nombres
                if (i == 0) {
                    return;
                }

                if(tableUsers.column(i).search() !== this.value){
                    tableUsers
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });
        
        var tableUsers = $('#tblUsers').DataTable({
            serverSide: true,
            processing: true,
            stateSave: true,
            "language": {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
            },
            ajax: {
                "type": "GET",
                "url": "/api/admin/users"+method,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'user', name: "users.name"},
                {data: 'surname', name: 'users.surname', className: 'd-none' },     //Columna oculta
                {data: 'alta', name: "users.created_at", className: 'text-right'},
                {data: 'email', name: "users.email"},
                {data: 'phones', name: "phones.phone_number"},
                {data: 'categories'},
                {data: 'image', className: 'text-center'},
                {data: 'acciones', name: "users.id", className: 'text-right'}
            ],
            // Indicamos que columnas se tienen que ocultar
            columnDefs: [
                {
                    targets: 1,
                    visible: false,
                    searchable: true
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
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/vehicles",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'type'},
                {data: 'car_license'},
                {data: 'marca'},
                {data: 'model'},
                {data: 'id', className: 'text-center'},
                {data: 'alta', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
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

    //Tabla Work Order Components:
    if($('#tblWorkOrderComponents').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var work_order = $('#workOrderId').val();

         $('#tblWorkOrderComponents').DataTable({
            serverSide: false,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/work-order-components/"+work_order,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'product', className: 'capitalize'},
                {data: 'supplied_qty', className: 'text-right'},
                {data: 'used_qty', className: 'text-right'},
                {data: 'returned_qty', className: 'text-right'}, 
                {data: 'estado'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }

    //Tabla Work Order Patterns:
    if($('#tblWorkOrderPatterns').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        $('#tblWorkOrderPatterns').DataTable({
            serverSide: true,
            /*processing: true,*/
            ajax: {
                "type": "GET",
                "url": "/api/admin/work-order-patterns",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', className: 'capitalize'},
                {data: 'patron'},
                {data: 'id', className: 'text-center'}, 
                {data: 'alta', className: 'text-right'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
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
            serverSide: false,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/work-order-products/"+work_order,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'product', className: 'capitalize'},
                {data: 'planned_qty', className: 'text-right'},
                {data: 'produced_qty', className: 'text-right'},
                {data: 'batches'}, 
                {data: 'estado'},
                {data: 'acciones', className: 'text-right'}
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

    //Tabla Workplaces:
    if($('#tblWorkplaces').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var company_id = $('#companyId').val();
        var business_relationship = $('#businessRelationship').val();
        $('#companyId').remove();
        
        $('#tblWorkplaces').DataTable({
            serverSide: true,
            /*processing: true,*/
            ajax: {
                "type": "GET",
                "url": "/api/admin/workplaces/"+company_id+"/"+business_relationship,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'Town'},
                {data: 'id', className: 'text-center'},
                {data: 'id', className: 'text-right'}
            ],
            columnDefs: [
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
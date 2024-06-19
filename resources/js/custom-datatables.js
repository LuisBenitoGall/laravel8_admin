/* DATATABLES: */

//Enlace para widget de traducciones:
function linkTranslations(objid, schema, title){
    return '<a href="#" class="btn btn-xs btn-info btn-translations" rel="tooltip" title="'+title+'" data-toggle="modal" data-target="#modal-translations" data-id="'+objid+'" data-schema="'+schema+'"><i class="fa fa-globe"></i></a>';
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
        language : {
            "url": "/js/plugins/dataTables/lang/"+idm+".json"
        },
        responsive: true,
        stateSave: false,       //Guarda página al refrescar. 
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todo"]],
        pageLength: 25,         //Resultados por página
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

    //Tabla Order Items:
    //31/05/2024: esta tabla permanece aquí pues se utiliza en más de una vista.
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
});
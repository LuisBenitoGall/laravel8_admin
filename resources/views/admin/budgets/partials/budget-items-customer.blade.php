<div class="row pt-3">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblOrderItemsShow">
			<thead>
				<tr>
					<th class="text-center">#</th>
					<th>{{ ucfirst(trans('textos.producto')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.cantidad')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.precio_unidad')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.jornadas')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.descuento')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.importe')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.acciones')) }}</th>
				</tr>
			</thead>

			<tfoot>
				@if($order->discount)
					<tr>
		                <td colspan="6" class="text-right"><b>{{ ucfirst(trans('textos.descuento')) }}</b></td>
		                <td class="text-right">{{ $order->discount }}%</td>
		                <td></td>
		            </tr>
				@endif

	            <tr>
	                <td colspan="6" class="text-right"><b>{{ trans('textos.importe_total') }} *</b></td>
	                <td class="text-right">{{ $order_amount }}{{ $currency->symbol }}</td>
	                <td></td>
	            </tr>

	            @if(isset($invoice) && $invoice->with_iva > 0)
		            <tr>
		                <td colspan="6" class="text-right"><b>{{ trans('textos.importe') }} + IVA ({{ $invoice->with_iva }}%)</b></td>
		                <td class="text-right">{{ round($invoice->amount_with_iva, 2) }}{{ $currency->symbol }}</td>
		                <td></td>
		            </tr>
		        @endif
	        </tfoot>
		</table>

		{{-- Aviso sobre cálculo importe de líneas --}}
		<div class="col-12">
			<small class="text-warning">
			* {{ trans('textos.importe_total_texto') }}
			</small>
		</div>
	</div>
</div>

@push('scripts')
<script>
$(function(){
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
});
</script>
@endpush
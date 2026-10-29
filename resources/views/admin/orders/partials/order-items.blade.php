@php
$colspan = $env == 'invoices'? 7:6;
$see_iva = $env == 'invoices'? true:false;
$tbl = $env == 'invoices'? 'tblInvoiceItems':'tblOrderItems';
@endphp

<input type="hidden" id="seeIva" value="{{ $see_iva }}">

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="{{ $tbl }}">
			<thead>
				<tr>
					<th class="text-center wdt50">#</th>
					<th>{{ ucfirst(trans('textos.producto')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.cantidad')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.precio_unidad')) }}</th>
					@if($env == 'invoices')
						<th class="text-center">IVA</th>
					@endif
					<th class="text-center">{{ ucfirst(trans('textos.jornadas')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.descuentos')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.importe')) }}</th>
					<th>{{ ucfirst(trans('textos.centro_coste')) }}</th>
					@if($env == 'invoices')
						<th>{{ ucfirst(trans('textos.pedidos_asociados')) }}</th>
					@endif
					<th>{{ ucfirst(trans('textos.estado')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.acciones')) }}</th>
				</tr>
			</thead>

			<tfoot>
				{{-- @if($order->discount)
					<tr>
		                <td colspan="{{ $colspan }}" class="text-right"><b>{{ ucfirst(trans('textos.descuento')) }}</b></td>
		                <td class="text-right">{{ $order->discount }}%</td>
		                <td></td>
		                <td></td>
		            </tr>
				@endif --}}

	            <tr>
	                <td colspan="{{ $colspan }}" class="text-right"><b>{{ trans('textos.importe_total') }} *</b></td>
	                <td class="text-right">{{ number_format($order_amount, 2, ',', '.') }}{{ $currency->symbol }}</td>
	                <td></td>
	                <td></td>
	                <td></td>
	                @if($env == 'invoices') <td></td> @endif
	            </tr>

	            @if(isset($invoice) && $invoice->with_iva > 0)
		            <tr>
		                <td colspan="{{ $colspan }}" class="text-right"><b>{{ trans('textos.importe') }} + IVA</b></td>
		                {{-- ({{ $invoice->with_iva }}%) --}}
		                <td class="text-right">{{ number_format($invoice->amount_with_iva, 2, ',', '.') }}{{ $currency->symbol }}</td>
		                <td></td>
		                <td></td>
		                <td></td>
		                @if($env == 'invoices') <td></td> @endif
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
        var invoice_id = $('#myInvoice').val();

        var tableInvoiceItems = $('#tblInvoiceItems').DataTable({
            serverSide: true,
            processing: true,
            // drawCallback: function() {
            //     $('.select2-dt').select2();
            // },
            ajax: {
                "type": "GET",
                "url": "/api/admin/order-items/"+order+"/"+environment+"/"+invoice_id,
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
                {data: 'linked_orders'},
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
});
</script>
@endpush
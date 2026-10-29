@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="productId" value="{{ $product->id }}">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
   		{{-- Editar producto --}}
   		@can('products.edit')
    		<a target="_blank" href="{{ route('products.edit', $product) }}" class="btn btn-info">
    			<i class="fa fa-edit"></i>
    			{!! ucfirst(trans('textos.producto_editar')) !!}
    		</a>
        @endcan
	</div>
</div>

<div class="row">
	<div class="col-12 mb-3">
		<h3>
			{!! ucfirst(trans('textos.historico_movimientos')) !!} 
			<u>{{ $product->name }}</u> 
		</h3>
	</div>

	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblProductStockMovements">
			<thead>
				<tr>
					<th class="text-center">{!! ucfirst(trans('textos.fecha')) !!}</th>
					<th>{!! ucfirst(trans('textos.movimiento')) !!}</th>
					<th>{!! ucfirst(trans('textos.almacen')) !!}</th>
					<th>{!! ucfirst(trans('textos.lote')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.cantidad')) !!}</th>
					<th class="text-center">Stock</th>
					<th class="text-center">{!! ucfirst(trans('textos.documento')) !!}</th>
					<th>{!! ucfirst(trans('textos.usuario')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
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
                {data: 'batch', name: 'batches.batch_num'},
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
});
</script>
@endpush

@endsection
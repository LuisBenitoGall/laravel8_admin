@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">

	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblInventory">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.producto')) !!}</th>
					<th>{!! ucfirst(trans('textos.referencia')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
					<th>{!! ucfirst(trans('textos.imagen')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.estado')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Inventory:
    if($('#tblInventory').length > 0){
        function format(d) {
            // `d` is the original data object for the row
            var total = 0;
            if (d.length > 0) {
                var tr = '';
                d.forEach(function (row, line) {
                    total = parseFloat(total) + parseFloat(row[2]);
                    tr += '<tr>' +
                        '<td colspan="1" class="no-border">' + row[0] + '</td>' +
                        '<td colspan="1" class="no-border">' + row[1] + '</td>' +
                        '<td colspan="1" class="no-border text-right">' + row[2] + '</td>' +
                    '</tr>';
                });
            } else {
                tr = '<tr>' +
                    '<td colspan="12" class="no-border">'+txtInfoSin+'</td>' +
                '</tr>';
            }

            var html = '<table class="subtable" cellpadding="0" cellspacing="0" border="0" width="100%">' +
            '<thead>' +
                '<tr>' +
                    '<th>'+txtLote+'</th>' +
                    '<th>'+txtAlmacen+'</th>' +
                    '<th class="text-center">'+txtCantidad+'</th>' +
                '</tr>' +
            '</thead>' +
            tr +
            '<tfoot>' +
                '<tr>' +
                    '<td class="no-border text-right" colspan="2"><b>' + txtTotal + '</b></td>' +
                    '<td class="no-border text-right"><b>' + total + '</b></td>' +
                '</tr>' +
            '</tfoot>' +
            '</table>';

            return html;
        }

        var tableInventory = $('#tblInventory').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/inventory",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'product', name: 'products.name'},
                {data: 'ref', name: 'products.ref'},
                {data: 'alta', className: 'text-right'},
                {data: 'image', className: 'text-center'},
                {data: 'status', className: 'text-center'},
                {data: 'acciones', className: 'text-right'}
            ]
        });

        $('#tblInventory tbody').on('click', 'td .btnSeeItem', function (e) {
            e.preventDefault();
            var tr = $(this).closest('tr');
            var row = tableInventory.row(tr);
            var product_id = $(this).data('product');
            var route = "/api/admin/inventory/" + product_id;

            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
            } else {
                $.ajax({
                    url: route,
                    cache: true
                })
                .done(function (data) {
                    row.child(format(data)).show();
                    tr.addClass('shown');
                });
            }
        });
    }
});
</script>
@endpush

@endsection
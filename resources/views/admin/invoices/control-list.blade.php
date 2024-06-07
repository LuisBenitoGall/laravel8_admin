@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="dtController" value="orders">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Listado facturas --}}
		@can('invoices.index')
			<a href="{{ route('invoices.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_facturas') !!}
			</a>
		@endcan
	</div>
</div>

{{-- Tabla --}}
<div class="row">
	<div class="col-12">
		<h4 class="text-warning mb-3">Relación de pedidos con facturación pendiente.</h4>
	</div>

	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblInvoiceControl">
			<thead>
				<tr>
					<th class="text-center wdt50">#</th>
					<th class="wdt150">{!! ucfirst(trans('textos.cliente')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.responsable')) !!}</th>
					<th class="d-none">&nbsp;</th>
					<th class="text-center wdt50">{!! ucfirst(trans('textos.fecha')) !!}</th>
					<th class="text-center wdt100">{!! trans('textos.importe') !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.pendiente')) !!}</th>
					<th class="wdt100">{!! trans('textos.estado') !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
    //Tabla Invoice Control:
    if($('#tblInvoiceControl').length > 0){
    	var tokenDT = $('meta[name="csrf-token"]').attr('content');

        //Filters:
        var jsonData = JSON.parse(localStorage.getItem('DataTables_tblInvoiceControl_'+window.location.pathname));

        $('#tblInvoiceControl thead tr').clone(true).appendTo('#tblInvoiceControl thead');
        $('#tblInvoiceControl thead tr:eq(1) th').each(function(i){
            var title = $(this).text(); 
            if(i == 5 || i == 6 || i == 7 || i == 8){
                $(this).html('');
            }else{
                let value = jsonData && jsonData.columns[i].search.search ? jsonData.columns[i].search.search : '';
                let htmlRaw = `<input type="text" class="DTsearch" placeholder="${txtBuscar+' '+title}" value="${value}" />`;
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
	                        tableInvoiceControl
	                            .column(2)
	                            .search(fullname[0])
	                            .column(3)
	                            .search('')
	                            .draw();
	                    }else{
	                        // La busqueda se hace en ambos campos (nombre y apellido)
	                        tableInvoiceControl
	                            .column(2)
	                            .search(fullname[0])
	                            .column(3)
	                            .search(fullname[1])
	                            .draw();
	                    }
	                });
	            }
            }

            $('input', this).on('keyup change', function(){
            	// Ignoramos la busqueda del campo de nombres
                if (i == 2) {
                    return;
                }

                if(tableInvoiceControl.column(i).search() !== this.value){
                    tableInvoiceControl
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });

        var tableInvoiceControl = $('#tblInvoiceControl').DataTable({
            serverSide: true,
            processing: true,
            stateSave: true,
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
                {data: 'surname', name: 'users.surname', className: 'd-none' },     //Columna oculta
                {data: 'delivery_date', className: 'text-right'},
                {data: 'amount', name: 'invoices.amount', className: 'text-right'},
                {data: 'pendiente', className: 'text-right'},
                {data: 'estado'},
                {data: 'acciones', className: 'text-right'}
            ],
            // Indicamos que columnas se tienen que ocultar
            columnDefs: [
                {
                    targets: 3,
                    visible: false,
                    searchable: true
                }
            ]
        });
    }
});
</script>
@endpush

@endsection
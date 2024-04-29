@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Nuevo presupuesto --}}
		@can('budgets.create')
			@if($budget_from == 'customers')
				<a href="{{ route('budgets.create') }}" class="btn btn-info">
					<i class="fa fa-plus"></i>
					{!! ucfirst(trans('textos.presupuesto_nuevo')) !!}
				</a>
			@endif
		@endcan
	</div>
</div>

<input type="hidden" id="dtFrom" value="{{ $budget_from }}">
<input type="hidden" id="dtController" value="budgets">

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblBudgets">
			<thead>
				<tr>
					<th class="text-center wdt100">#</th>
					{{-- 07/12/22: se oculta esta columna a petición de Cuentas --}}
					{{-- <th class="text-center wdt100">{!! ucfirst(trans('textos.numero_antiguo')) !!}</th> --}}
					<th class="wdt150">{!! ucfirst(trans('textos.presupuesto')) !!}</th>
					{{-- Manager --}}
					<th class="wdt200">
						@if($budget_from == 'customers')
							{!! ucfirst(trans('textos.responsable')) !!}
						@else
							{!! ucfirst(trans('textos.comprador')) !!}
						@endif
					</th>
					<th class="d-none">&nbsp;</th>
					<th class="wdt100">{!! ucfirst(trans('textos.pedido')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.importe')) !!}</th>
					{{-- <th class="wdt100">{!! ucfirst(trans('textos.modalidad')) !!}</th> --}}
					<th class="text-center wdt100">{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
					<th class="wdt100">
						@if($budget_from == 'customers')
							{!! ucfirst(trans('textos.cliente')) !!}
						@else
							{!! ucfirst(trans('textos.proveedor')) !!}
						@endif
					</th>
					<th class="wdt200">
						{!! ucfirst(trans('textos.cliente_final')) !!}
					</th>
					<th class="wdt100">{!! ucfirst(trans('textos.estado')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Budgets:
    if($('#tblBudgets').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var from = $('#dtFrom').val();

        // Filters:
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
});
</script>
@endpush

@endsection
@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="dtYear" value="{{ $year }}">
<input type="hidden" id="dtWorkplace" value="{{ $workplace_id }}">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		@can('orders.index')
			<a href="{{ route('orders.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_pedidos') !!}
			</a>
		@endcan
	</div>
</div>

{{-- Filtro --}}
<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('order-production-prevision.filter-control-panel') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Año --}}
						@include('admin.partials.selector-year', ['selected_year' => $year])

						{{-- Centros de trabajo --}}
						@if(count($workplaces) > 1)
							<label for="workplace" class="col-2 col-form-label">{{ ucfirst(trans('textos.centro_trabajo')) }}</label>
	                    	<div class="col-3">
	                    		<x-select name="workplace" class="select2" label="centro_trabajo_selec" :options="$workplaces" value="{{ $workplace_id }}"/>
	                    	</div>
	                    @elseif(count($workplaces) == 1)
	                    	@foreach($workplaces as $i => $wk)
	                    		<input type="hidden" name="workplace" value="{{ $i }}">
	                    	@endforeach
	                    @endif

						<x-btn-submit name="submit_filter" label="{{ trans('textos.buscar') }}" inline/>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="row">
    {{-- Columnas --}}
    @php
    $columns = [
        ['produccion_prevista', 'wdt150'],
        ['cliente', 'wdt200'],
        ['pedido_num_abrv', 'text-center wdt100'],
        ['proyecto', 'text-center wdt100'],
        ['oc', 'wdt100'],
        ['pedido_titulo', 'wdt200'],
        ['manager', 'wdt150'],
        ['', 'd-none'],
        ['cliente_manager', 'wdt200'],
        ['cliente_final', 'wdt150'],
        ['', 'd-none'],
        ['importe', 'text-center wdt100'],
        ['estado', 'wdt100'],
        ['centro_trabajo', 'wdt100'],
        ['acciones', 'text-center wdt100']
    ]
    @endphp 

    <div class="col-12 col-md-4 col-lg-3">
        <div class="ibox collapsed dt-columns">
            <div class="ibox-title bg-success">
                <h5>{{ trans('textos.columnas_mostrar_ocultar') }}</h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content" style="display: none;">
                <ul>
                    @foreach($columns as $i => $col)
                        <li class="row-{{ $i }}" data-visible="">
                            <a href="#" class="toggle-vis" data-column="{{ $i }}">{{ ucfirst(trans('textos.'.$col[0])) }}</a>

                            {{-- <i class="fa fa-check text-success"></i> --}}
                        </li>
                    @endforeach 
                </ul>                    
            </div>
        </div>
    </div>

	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblOrderProductionPrevisions">
			<thead>
				<tr>
                    @foreach($columns as $col)
                        <th class="{{ $col[1] }} nowrap">{{ ucfirst(trans('textos.'.$col[0])) }}</th>
                    @endforeach
				</tr>
			</thead>

			<tfoot>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
                    <td></td>
                    <td></td>
                    <td></td>
					<td class="text-right xxx"><b>{{ trans('textos.total') }}</b></td>
					<td class="text-right"></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>	
			</tfoot>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Order Production Previsions:
    if($('#tblOrderProductionPrevisions').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var year = $('#dtYear').val();
        var workplace = $('#dtWorkplace').val();

        //Filters:
        // Load data from localstorage
        var jsonData = JSON.parse(localStorage.getItem('DataTables_tblOrderProductionPrevisions_'+window.location.pathname));

        $('#tblOrderProductionPrevisions thead tr').clone(true).appendTo('#tblOrderProductionPrevisions thead');
        $('#tblOrderProductionPrevisions thead tr:eq(1) th').each(function(i){
            var title = $(this).text(); 

            if(i == 14){
                $(this).html('');
            }else{
                // Check field on json of localstorage
                let value = jsonData && jsonData.columns[i].search.search ? jsonData.columns[i].search.search : '';
                let htmlRaw = `<input type="text" class="DTsearch" placeholder="${txtBuscar+' '+title}" value="${value}" />`
                $(this).html(htmlRaw);
                let element = $(this).html(htmlRaw);

                //Búsqueda por nombre y apellidos de manager proveedor.
                if (i == 5) {
                    $('input', element).on('keyup change', function(){
                        // Diferenciamos si es nombre o apellido
                        let fullname = this.value.split(' ');

                        // Si es un solo nombre, se busca en el campo nombre dejando el campo apellido vacio
                        // caso contrario se busca en ambos campos
                        if(fullname.length == 1) {
                            // La busqueda se hace en el campo nombre
                            tableOrderProductionPrevisions
                                .column(5)
                                .search(fullname[0])
                                .column(6)
                                .search('')
                                .draw();
                        }else{
                            // La busqueda se hace en ambos campos (nombre y apellido)
                            tableOrderProductionPrevisions
                                .column(5)
                                .search(fullname[0])
                                .column(6)
                                .search(fullname[1])
                                .draw();
                        }
                    });
                }

                //Búsqueda por nombre y apellidos de manager cliente.
                if (i == 10) {
                    $('input', element).on('keyup change', function(){
                        // Diferenciamos si es nombre o apellido
                        let fullname_ = this.value.split(' ');

                        // Si es un solo nombre, se busca en el campo nombre dejando el campo apellido vacio
                        // caso contrario se busca en ambos campos
                        if(fullname.length == 1) {
                            // La busqueda se hace en el campo nombre
                            tableOrderProductionPrevisions
                                .column(10)
                                .search(fullname_[0])
                                .column(11)
                                .search('')
                                .draw();
                        }else{
                            // La busqueda se hace en ambos campos (nombre y apellido)
                            tableOrderProductionPrevisions
                                .column(10)
                                .search(fullname_[0])
                                .column(11)
                                .search(fullname_[1])
                                .draw();
                        }
                    });
                }
            }
         
            $('input', this).on('keyup change', function(){
                if(tableOrderProductionPrevisions.column(i).search() !== this.value){
                    tableOrderProductionPrevisions
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });

        var tableOrderProductionPrevisions = $('#tblOrderProductionPrevisions').DataTable({
            order: [[ 0, "desc" ]],
            serverSide: false, 	//10/09/24: en false para permitir la suma de totales del footer y el filtrado por la columna fecha. 
            processing: true,
            autoWidth: false,
            // drawCallback: function(){
            //     $('.select2-dt').select2();
            // },
            language: {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
            },
            ajax: {
                "type": "GET",
                "url": "/api/admin/order-production-prevision/"+year+"/"+workplace,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'fecha', name: 'order_production_previsions.month'},
                {data: 'customer', name: 'companies.name'},
                {data: 'order_num', name: 'orders.order_num', className: 'text-right'},
                {data: 'project', name: 'projects.project_num', className: 'text-right'},
                {data: 'purchase_num', name: 'orders.purchase_num', className: 'text-right'},
                {data: 'name', name: 'companies.name'},
                {data: 'manager', name: 'users.name'},
                {data: 'surname', name: 'users.surname', className: 'd-none'},
                {data: 'customer_manager', name: 'users.name'},
                {data: 'CustomerManagerSurname', name: 'users.surname', className: 'd-none'},
                {data: 'final_customer', name: 'companies.name'},
                {data: 'amount', name: 'amount', className: 'text-right'},
                {data: 'estado', name: 'estado'},
                {data: 'workplace', name: 'workplaces.name'},
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
                .column(11, { search: 'applied' })
                .data()
                .reduce(function(a, b){
                    return intVal(a) + intVal(b);
                }, 0);

                $(api.column(11).footer()).html(
                    total.toFixed(2)+currency   
                );
            }
        });

        //Obtener número de columnas:
        //13/09/24: revisar porque no cuenta correctamente el número de columnas.
        //var num_cols = $('#tblOrderProductionPrevisions thead th').length;
        var num_cols = 15;

        //Seteando estado de vista inicial de la columna:
        for (var i = 0; i < num_cols; i++){
            if(tableOrderProductionPrevisions.column(i).visible()){
                $('.row-'+i).append('<i class="fa fa-check text-success"></i>');
                $('.row-'+i).data('visible', 1);
            }else{
                $('.row-'+i).data('visible', 2);    
            }
        }

        //Mostrar / ocultar columnas:
        document.querySelectorAll('a.toggle-vis').forEach((el) => {
            el.addEventListener('click', function (e) {
                e.preventDefault();
                var parent = $(this).closest('li');
                is_ok = parent.find('.fa');

                if(parent.data('visible') == 1){
                    parent.find('.fa').remove();
                    parent.data('visible', 2);
                }else{
                    parent.append('<i class="fa fa-check text-success"></i>');
                    parent.data('visible', 1);
                }
         
                let columnIdx = e.target.getAttribute('data-column');
                let column = tableOrderProductionPrevisions.column(columnIdx);

                // Toggle the visibility
                column.visible(!column.visible());
            });
        });
	}
});
</script>
@endpush

@endsection
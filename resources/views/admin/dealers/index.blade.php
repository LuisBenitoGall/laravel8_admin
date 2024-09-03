@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('dealers.create')
			<a class="btn btn-info" href="{{ route('dealers.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.repartidor_nuevo')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblDealers">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.repartidor')) !!}</th>
                    <th class="d-none">&nbsp;</th>
                    <th class="text-center">{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
					<th>{!! ucfirst(trans('textos.email')) !!}</th>
					<th>{!! ucfirst(trans('textos.telefonos')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.imagen')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Dealers:
    if($('#tblDealers').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        //Filters:
        //Load data from localstorage
        var jsonData = JSON.parse(localStorage.getItem('DataTables_tblDealers_'+window.location.pathname));

        $('#tblDealers thead tr').clone(true).appendTo('#tblDealers thead');
        $('#tblDealers thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 1 || i == 6){
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
                            tableDealers
                                .column(0)
                                .search(fullname[0])
                                .column(1)
                                .search('')
                                .draw();
                        }else{
                            // La busqueda se hace en ambos campos (nombre y apellido)
                            tableDealers
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

                if(tableDealers.column(i).search() !== this.value){
                    tableDealers
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });

        var tableDealers = $('#tblDealers').DataTable({
            serverSide: true,
            processing: true,
            stateSave: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/dealers",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'user', name: "users.name"},
                {data: 'surname', name: 'users.surname', className: 'd-none' },     //Columna oculta
                {data: 'created_at', name: "users.created_at", className: 'text-right'},
                {data: 'email', name: "users.email"},
                {data: 'phones', name: "phones.phone_number"},
                {data: 'image', className: 'text-center'},
                {data: 'acciones', className: 'text-right'}
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
});
</script>
@endpush

@endsection

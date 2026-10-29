@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="myCompany" value="@if($related_company){{ $related_company->id }}@endif">
<input type="hidden" id="myStatus" value="{{ $status }}">

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		{{-- Nuevo contacto --}}
		@can('contacts.create')
			<a href="{{ route('contacts.create') }}" class="btn btn-info"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.contacto_nuevo')) !!}</a>
		@endcan

        {{-- Nuevo contacto empresa seleccionada --}}
        @can('contacts.create')
            @if($related_company)
                <a href="{{ route('contacts.create', $related_company->id) }}" class="btn btn-info"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.contacto_nuevo')) !!} <b>{!! $related_company->name !!}</b></a>
            @endif
        @endcan
	</div>
</div>

{{-- Formulario filtro --}}
@include('admin.contacts.partials.form-filter')

<div class="row">
	<div class="table-responsive toggle-view-list">
		<table class="table table-hover dtabla" id="tblContacts">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.usuario')) !!}</th>
					<th class="d-none">&nbsp;</th>
					<th class="text-center">{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
					<th>{!! ucfirst(trans('textos.email')) !!}</th>
					<th>{!! ucfirst(trans('textos.telefonos')) !!}</th>
					{{-- <th>{!! ucfirst(trans('textos.categorias')) !!}</th> --}}
                    <th>{!! ucfirst(trans('textos.posicion')) !!}</th>
                    <th>{!! ucfirst(trans('textos.estado')) !!}</th>
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
	//Tabla Contacts:
    if($('#tblContacts').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var company_id = $('#myCompany').val();
        var status = $('#myStatus').val();

        //Filters:
        // Load data from localstorage
        var jsonData = JSON.parse(localStorage.getItem('DataTables_tblContacts_'+window.location.pathname));

        $('#tblContacts thead tr').clone(true).appendTo('#tblContacts thead');
        $('#tblContacts thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 7 || i == 8){
                $(this).html('');
            }else{
                // Check field on json of localstorage
                let value = jsonData && jsonData.columns[i].search.search ? jsonData.columns[i].search.search : '';
                let htmlRaw = `<input type="text" class="DTsearch" placeholder="${txtBuscar+' '+title}" value="${value}" />`
                $(this).html(htmlRaw);
                let element = $(this).html(htmlRaw);

                //Búsqueda por nombre y apellidos.
                if(i == 0){
                    $('input', element).on('keyup change', function(){
                        // Diferenciamos si es nombre o apellido
                        let fullname = this.value.split(' ');

                        // Si es un solo nombre, se busca en el campo nombre dejando el campo apellido vacio
                        // caso contrario se busca en ambos campos
                        if(fullname.length == 1) {
                            // La busqueda se hace en el campo nombre
                            tableContacts
                                .column(0)
                                .search(fullname[0])
                                .column(1)
                                .search('')
                                .draw();
                        }else{
                            // La busqueda se hace en ambos campos (nombre y apellido)
                            tableContacts
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

                if(tableContacts.column(i).search() !== this.value){
                    tableContacts
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });

        var tableContacts = $('#tblContacts').DataTable({
            serverSide: true,
            processing: true,
            stateSave: true,
            language: {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
            },
            ajax: {
                type: "POST",
                dataType: "json",
                data: function(d) {
                    d._token = $('meta[name="csrf-token"]').attr('content');
                    d.related_company = $('#myCompany').val();
                    d.status = $('#myStatus').val();
                },
                url: "/api/admin/contacts",
                error: function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'user', name: "users.name"},
                {data: 'surname', name: 'users.surname', className: 'd-none' },     //Columna oculta
                {data: 'alta', name: "users.created_at", className: 'text-right'},
                {data: 'email', name: "users.email"},
                {data: 'phones', name: "phones.phone_number"},
                {data: 'position', name: "contacts.position"},
                {data: 'status', name: "contacts.status"},
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
});
</script>
@endpush

@endsection
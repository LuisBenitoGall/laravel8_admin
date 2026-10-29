@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="all" value="{{ $all }}">
<input type="hidden" id="usersStatus" value="{{ $status }}">

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		{{-- Toggle vista - cuadrícula --}}
        @include('admin.partials.toggle-views', ['cookie' => 'users'])

        {{-- Nuevo usuario --}}
		@can('users.create')
			<a href="{{ route('users.create') }}" class="btn btn-info"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.usuario_nuevo')) !!}</a>
		@endcan

        {{-- Nuevo usuario para empresa seleccionada --}}
        @if(isset($related_company) && $related_company)
            <a href="{{ route('users.create') }}" class="btn btn-info"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.usuario_nuevo')) !!} <b>{{ $related_company->name }}</b></a>        
        @endif
	</div>
</div>

{{-- Formulario filtro --}}
@if(!$all)
    @include('admin.users.partials.form-filter')
@endif

{{-- Vista listado --}}
<div class="row">
	<div class="table-responsive toggle-view-list">
		<table class="table table-hover dtabla" id="tblUsers">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.usuario')) !!}</th>
					<th class="d-none">&nbsp;</th>
					<th class="text-center">{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
					<th>{!! ucfirst(trans('textos.email')) !!}</th>
					<th>{!! ucfirst(trans('textos.telefonos')) !!}</th>
					{{-- <th>{!! ucfirst(trans('textos.categorias')) !!}</th> --}}
                    <th>{!! ucfirst(trans('textos.role')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.imagen')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

{{-- Vista cuadrícula --}}
<div class="toggle-view-grid">
	{{-- Componente Users Grid --}}
	@livewire('users-grid')

	@include('admin.partials.back-to-top')

	{{-- <div class="row"> --}}
		{{-- @foreach($users as $row)
			<div class="col-sm-12 col-md-6 col-lg-3 mb-3">
				<div class="user">
					<h5>
						@can('users.destroy')
							<a href="{{ route('users.edit', $row->id) }}">{{ $row->name }} {{ $row->surname }}</a>
						@else
							<label>{{ $row->name }} {{ $row->surname }}</label>
						@endcan
					</h5>

					<div class="user-image">
						@if($row->Image)
							<img src="{!! asset('img/users/'.$row->Image) !!}" class="img-fluid" alt="{{ $row->name }} {{ $row->surname }}">
						@else
							<div class="product-image-default">
								<i class="fa fa-user"></i>
							</div>
						@endif	
					</div>	

					<div class="user-data">
						{{ ucfirst(trans('textos.fecha_alta')) }}: <b>{{ $row->created_at->format(config('locale.languages.'.config('app.locale'))[4]) }}</b>

						@can('users.destroy')
							<a href="{{ route('users.edit', $row->id) }}" class="pull-right">
								<i class="fa fa-edit" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"></i>
							</a>
						@endcan
						<br>

						Email: <b>{{ $row->email }}</b>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		@endforeach --}}
	{{-- </div> --}}
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Users:
    if($('#tblUsers').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var all = $('#all').val();
        var status = $('#usersStatus').val() && $('#usersStatus').val() != ''? $('#usersStatus').val():false;
        var method = all? 'all':'x';

        //Filters:
        // Load data from localstorage
        var jsonData = JSON.parse(localStorage.getItem('DataTables_tblUsers_'+window.location.pathname));

        $('#tblUsers thead tr').clone(true).appendTo('#tblUsers thead');
        $('#tblUsers thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 6 || i == 7){
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
                            tableUsers
                                .column(0)
                                .search(fullname[0])
                                .column(1)
                                .search('')
                                .draw();
                        }else{
                            // La busqueda se hace en ambos campos (nombre y apellido)
                            tableUsers
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

                if(tableUsers.column(i).search() !== this.value){
                    tableUsers
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });
        
        var tableUsers = $('#tblUsers').DataTable({
            serverSide: true,
            processing: true,
            stateSave: true,
            language: {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
            },
            ajax: {
                "type": "GET",
                "url": "/api/admin/users/"+method+"/"+status,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'user', name: "users.name"},
                {data: 'surname', name: 'users.surname', className: 'd-none' },     //Columna oculta
                {data: 'alta', name: "users.created_at", className: 'text-right'},
                {data: 'email', name: "users.email"},
                {data: 'phones', name: "phones.phone_number"},
                {data: 'roles', name: "roles.name"},
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
@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		{{-- Toggle vista - cuadrícula --}}
        {{-- @include('admin.partials.toggle-views', ['cookie' => 'projects']) --}}

        {{-- Nuevo proyecto --}}
		@can('projects.create')
    		<a href="{{ route('projects.create') }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.proyecto_nuevo')) !!}
    		</a>
        @endcan

        {{-- Favoritos --}}
        <a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-favourites">
			<i class="fa fa-star"></i>
			{!! ucfirst(trans('textos.favoritos')) !!}
		</a>

        {{-- Estadísticas --}}
        <a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-statistics">
			<i class="fa fa-chart-bar"></i>
			{!! ucfirst(trans('textos.estadisticas')) !!}
		</a>
	</div>
</div>

<input type="hidden" id="dtController" value="projects">

@php
$status_values = session('status_')? session('status_'):false;
$str_status_values = $status_values? implode(',', $status_values):'';
@endphp

<input type="hidden" id="dtStatus" value="{{ $str_status_values }}">

{{-- Filtros --}}
<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('projects.list') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}	

					<div class="form-group row">
						<div class="col-md-10 offset-md-2">
							<p class="text-warning">{!! trans('textos.selec_proyecto_estado') !!}</p>
						</div>

						{{-- Estados de proyecto --}}
						<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.estado')) !!}*</label>
						<div class="col-lg-6">
							<x-select name="status[]" class="select2" multiple="true" :options="$status" :values="$status_values"/>
						</div>

						<x-btn-submit name="submit_filter" label="{{ trans('textos.buscar') }}" inline="true"/>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

{{-- Vista listado --}}
<div class="row">
	<div class="table-responsive toggle-view-list">
		<table class="table table-hover dtabla" id="tblProjects">
			<thead>
				<tr>
					<th class="text-center">#</th>
					<th>{!! ucfirst(trans('textos.proyecto')) !!}</th>
					<th>{!! ucfirst(trans('textos.manager')) !!}</th>
					<th>{!! ucfirst(trans('textos.cliente')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.entrega_prevista')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fecha_entrega')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.prevision_horas')) !!}</th>
					<th></th>
					<th>{!! ucfirst(trans('textos.categorias')) !!}</th>
					<th>{!! ucfirst(trans('textos.progreso')) !!}</th>
					<th>{!! ucfirst(trans('textos.estado')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>

			<tfoot>
				<tr>
					<td class="text-right" colspan="7"><b>{{ trans('textos.prevision_horas') }} / {{ trans('textos.pendientes') }}</b></td>
					<td class="text-right"></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>

{{-- Vista cuadrícula --}}
{{-- 24/02/2024: desactivamos temporalmente esta opción --}}
{{-- <div class="toggle-view-grid"> --}}
	{{-- Componente Projects Grid --}}
	{{-- @livewire('projects-grid')

	@include('admin.partials.back-to-top')
</div> --}}

{{-- Modals: --}}
{{-- Favoritos --}}
@include('admin.projects.partials.modal-favourites')

{{-- Statistics --}}
@include('admin.projects.partials.modal-statistics')

@push('scripts')
<script>
$(function(){
	//Tabla Projects:
    if($('#tblProjects').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var status = $('#dtStatus').val();

        //Filters:
        var jsonData = JSON.parse(localStorage.getItem('DataTables_tblProjects_'+window.location.pathname));

        $('#tblProjects thead tr').clone(true).appendTo('#tblProjects thead');
        $('#tblProjects thead tr:eq(1) th').each(function(i){
            var title = $(this).text(); 
            if(i == 7 || i == 8 || i == 10 || i == 12){
                $(this).html('');
            }else{
                let value = jsonData && jsonData.columns[i].search.search ? jsonData.columns[i].search.search : '';
                let htmlRaw = `<input type="text" class="DTsearch" placeholder="${txtBuscar+' '+title}" value="${value}" />`
                $(this).html(htmlRaw);

                // $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
                // aquí el código retirado
                
            }

            $('input', this).on('keyup change', function(){
                if(tableProjects.column(i).search() !== this.value){
                    tableProjects
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });

        var tableProjects = $('#tblProjects').DataTable({
            serverSide: true,
            processing: true,
            stateSave: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/projects/"+status,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'num', className: 'text-right', name: 'projects.project_num'},
                {data: 'name', name: 'projects.name'},
                {data: 'manager', name: 'users.name'},
                {data: 'customer', name: 'companies.name'},
                {data: 'alta', className: 'text-right'},
                {data: 'entrega_prevista', className: 'text-right'},
                {data: 'entrega', className: 'text-right'},
                {data: 'hours_forecast', className: 'text-right'},
                {data: 'hours_worked', visible: false},
                {data: 'category_names', name: 'category_names', searchable: true},
                {data: 'progress', className: 'position-relative'},
                {data: 'estado', name: 'estado'},
                {data: 'acciones', orderable: false, searchable: false, className: 'text-right'}
            ],
            "footerCallback": function(row, data, start, end, display){
                var api = this.api(), data;

                var intVal = function(i){
                    return i;
                };

                //Total por filtro aplicado:
                hours_forecast = api
                .column(7, { search: 'applied' })
                .data()
                .reduce(function(a, b){
                    return intVal(a) + intVal(b);
                }, 0);

                //Horas trabajadas:
                hours_worked = api
                .column(8, { search: 'applied' })
                .data()
                .reduce(function(a, b){
                    return intVal(a) + intVal(b);
                }, 0);

                var pending = hours_forecast - hours_worked;

                $(api.column(7).footer()).html(
                    hours_forecast.toFixed(2)+' / '+pending.toFixed(2)
                );
            }
        });
    }
	
	//Agregar favorito.
	// 21/06/2023: se migra el método a custom.js pues va a ser utilizado también por projects/edit.blade.php

	//Modal favoritos:
	var favourites = {!! json_encode($favourites) !!};
	if(favourites.length > 0){
		$('#modal-favourites').modal({
            backdrop: 'static',
            keyboard: false,
            toggle: true
        });	
	}
});
</script>
@endpush

@endsection
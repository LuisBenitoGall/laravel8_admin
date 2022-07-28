@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		{{-- Toggle vista - cuadrícula --}}
        @include('admin.partials.toggle-views', ['cookie' => 'projects'])

        {{-- Nuevo proyecto --}}
		@can('projects.create')
    		<a href="{{ route('projects.create') }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.proyecto_nuevo')) !!}
    		</a>
        @endcan

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
					<th>{!! ucfirst(trans('textos.proyecto')) !!}</th>
					<th>{!! ucfirst(trans('textos.cliente')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.entrega_prevista')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fecha_entrega')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.prevision_horas')) !!}</th>
					<th></th>
					<th>{!! ucfirst(trans('textos.progreso')) !!}</th>
					<th>{!! ucfirst(trans('textos.estado')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>

			<tfoot>
				<tr>
					<td class="text-right" colspan="5"><b>{{ trans('textos.prevision_horas') }} / {{ trans('textos.pendientes') }}</b></td>
					<td class="text-right"></td>
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
<div class="toggle-view-grid">
	{{-- Componente Projects Grid --}}
	@livewire('projects-grid')

	@include('admin.partials.back-to-top')
</div>

{{-- Modals: --}}
{{-- Statistics --}}
@include('admin.projects.partials.modal-statistics')

@endsection
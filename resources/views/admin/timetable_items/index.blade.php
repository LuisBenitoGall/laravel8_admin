@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Nuevo concepto --}}
		@can('timetable-items.create')
    		<a href="{{ route('timetable-items.create') }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.concepto_nuevo')) !!}
    		</a>
        @endcan

        {{-- Exportar valores --}}
		@if(count(session('companies')) > 1)
			<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-export-values">
				<i class="fa fa-file-export"></i> {!! ucfirst(trans('textos.exportar_valores')) !!}	
			</a>
		@endif

		{{-- Configuración horarios --}}
		@can('timetable-items.create')
			<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-settings">
				<i class="fa fa-cogs"></i> {!! ucfirst(trans('textos.horarios_configuracion')) !!}	
			</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblTimetableItems">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.concepto')) !!}</th>
					<th>{!! ucfirst(trans('textos.tipo')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fijo')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.hora_min')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.tiempo_min')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.tiempo_max')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.veces_dia')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.icono')) !!}</th>
					<th>{!! ucfirst(trans('textos.color')) !!}</th>
					<th>{!! ucfirst(trans('textos.revision')) !!}</th>
	                <th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

{{-- Modals --}}
{{-- Export values --}}
@include('admin.partials.modal-export-values', ['action' => 'timetable-items.export'])

{{-- Timetable settings --}}
@include('admin.timetable_settings.partials.modal-settings')

@endsection
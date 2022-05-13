@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="dtController" value="plannings">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Crear planning --}}
		@can('planning.create')
			<a href="{{ route('planning.create') }}" class="btn btn-info">
				<i class="fa fa-plus"></i>
				{!! trans('textos.planificacion_nueva') !!}
			</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblPlannings">
			<thead>
				<tr>
					<th class="text-center">#</th>
					<th>{!! ucfirst(trans('textos.titulo')) !!}</th>
					<th class="text-center wdt150">{!! ucfirst(trans('textos.entrega_prevista')) !!}</th>
					<th class="text-center wdt150">{!! ucfirst(trans('textos.fecha_entrega')) !!}</th>
					<th>{!! ucfirst(trans('textos.estado')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@endsection
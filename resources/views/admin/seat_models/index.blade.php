@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Ver todos los asientos --}}
		<a href="{{ route('seats.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_asientos_contables') !!}
		</a>

		{{-- Nuevo modelo  --}}
		@can('seats.create')
			<a class="btn btn-info" href="{{ route('seat-models.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.modelo_nuevo')) !!}</a>
		@endcan	
	</div>
</div>

<div class="row mb-3">
	<div class="col-12 mb-3">
		<h3>{!! ucfirst(trans('textos.modelos')) !!} <b>{{ $company->name }}</b></h3>
	</div>

	<div class="table-responsive">
		<table class="table table-hover dTable" id="tblSeatModels">
			<thead>
				<tr>
					<th class="text-center wdt50">{!! ucfirst(trans('textos.fecha')) !!}</th>
					<th class="text-center wdt50">{!! ucfirst(trans('textos.modelo')) !!}</th>
					<th class="wdt50">{!! ucfirst(trans('textos.diario')) !!}</th>
					<th class="wdt50">{!! ucfirst(trans('textos.descripcion')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>

	</div>
</div>

@endsection
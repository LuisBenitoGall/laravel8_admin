@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('contracts.create')
			<a class="btn btn-info" href="{{ route('contracts.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.contrato_nuevo')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblContracts">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.contrato')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.duracion')) !!} <small>({!! trans('textos.dias') !!})</small></th>
					<th class="text-center">{!! ucfirst(trans('textos.incluye_vacaciones')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@endsection
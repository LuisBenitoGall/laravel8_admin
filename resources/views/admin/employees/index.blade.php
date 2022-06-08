@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones --}}
	<div class="col-12">
		@can('employees.create')
			<a class="btn btn-info" href="{{ route('users.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.empleado_nuevo')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblEmployees">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.empleado')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.inicio')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fin')) !!}</th>
					<th>{!! ucfirst(trans('textos.puesto')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.contrato_estado')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@endsection
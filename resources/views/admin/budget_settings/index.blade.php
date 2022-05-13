@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-sm-12">
        {{-- Acciones: --}}
		@can('budget-settings.create')
			<a class="btn btn-info" href="{{ route('budget-settings.create') }}">
				<i class="fa fa-plus"></i>
				{!! ucfirst(trans('textos.plantilla_nueva')) !!}
			</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblBudgetSettings">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.nombre')) !!}</th>
	                <th>{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
	                <th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@endsection
@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('product-patterns.create')
			<a class="btn btn-info" href="{{ route('product-patterns.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.patron_nuevo')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblProductPatterns">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.nombre')) !!}</th>
					<th>{!! ucfirst(trans('textos.patron')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@endsection
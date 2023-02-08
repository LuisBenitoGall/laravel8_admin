@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones --}}
	<div class="col-12">
		{{-- Nuevo banco --}}
		@can('banks.create')
			<a class="btn btn-info" href="{{ route('banks.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.banco_nuevo')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblBanks">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.banco')) !!}</th>
					<th>{!! ucfirst(trans('textos.nombre_comercial')) !!}</th>
					<th>SWIFT / BIC</th>
					<th>{!! ucfirst(trans('textos.codigo_europeo')) !!}</th>
					<th>LEI</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@endsection
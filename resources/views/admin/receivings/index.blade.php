@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	{{-- <div class="col-12">
		@can('companies.create')
			<a class="btn btn-info" href="{{ route('companies.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.empresa_nueva')) !!}</a>
		@endcan
	</div> --}}
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblReceivings">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.razon_social')) !!}</th>
					<th>{!! ucfirst(trans('textos.nombre_comercial')) !!}</th>
					<th>{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
					<th>NIF</th>
					<th>Logo</th>
					<th>{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@endsection
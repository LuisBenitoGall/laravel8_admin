@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('roles.create')
			<a class="btn btn-info" href="{{ route('roles.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.rol_nuevo')) !!}</a>

			{{-- Regenerar .json menú secundario y funcionalidades --}}
			<a class="btn btn-info btn-confirm" href="{{ route('roles.sync') }}" data-msg="{{ trans('textos.roles_regenerar_json_texto') }}"><i class="fa fa-sync"></i> {!! ucfirst(trans('textos.roles_regenerar_json')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblRoles">
			<thead>
				<tr>
					<th>Rol</th>
					<th>{!! ucfirst(trans('textos.descripcion')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@endsection
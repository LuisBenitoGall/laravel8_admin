@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="moduleId" value="{{ $module->id }}">

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can($permission.'.edit')
			<a class="btn btn-info" href="{{ route('categories.create', $module->id) }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.categoria_nueva')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblCategories">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.categoria')) !!}</th>
					<th>{!! ucfirst(trans('textos.nivel')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@endsection
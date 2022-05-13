@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-sm-12">
		<a href="{{ route('roles.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_roles') !!}
		</a>
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('roles.update', $role->id) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					
					@include('admin.roles.partials.form')

				</form>
			</div>
		</div>
	</div>
</div>

@endsection
@extends('admin.partials.layout')

@section('content')

<div class="row">
	{{-- Acciones: --}}
	<div class="col-sm-12">
		<a href="{{ route('roles.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_roles') !!}
		</a>
	</div>
</div>
<br>

<div class="row">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('roles.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					
					@include('admin.roles.partials.form')

				</form>
			</div>
		</div>
	</div>
</div>

@endsection
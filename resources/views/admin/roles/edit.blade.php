@extends('admin.partials.layout')

@section('content')

@php
$scroll_top = true;
@endphp

<input type="hidden" id="roleId" value="{{ $role->id }}">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-sm-12">
		{{-- Volver a roles --}}
		<a href="{{ route('roles.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_roles') !!}
		</a>

		{{-- Copiar roles a otras empresas --}}
		@if($role->name != config('constants.SUPER_ADMIN_'))
			<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-copy">
				<i class="fa fa-clone"></i>
				{!! trans('textos.roles_copiar') !!}
			</a>
		@endif
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('roles.update', $role->id) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}

					@include('admin.roles.partials.form')

			</div>
		</div>
	</div>
</div>

{{-- Modals: --}}
@include('admin.roles.partials.modal-copy')

{!! App\Helpers\helpers::scrollToTop() !!}

@endsection
@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		<a href="{{ route('order-settings.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_plantillas') !!}
		</a>

		@can('order-settings.create')
			<a class="btn btn-info" href="{{ route('order-settings.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.plantilla_nueva')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('order-settings.update', $template) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}

					@include('admin.order_settings.partials.form', ['action' => 'update'])
			
					<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection
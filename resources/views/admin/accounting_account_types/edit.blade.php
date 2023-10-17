@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-sm-12">
		{{-- Volver a grupos contables --}}
		<a href="{{ route('accounting-account-types.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_grupos_contables') !!}
		</a>

		{{-- Nuevo grupo contable --}}
		@can('accounting-account-types.create')
			<a class="btn btn-info" href="{{ route('accounting-account-types.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.grupo_nuevo')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-xs-12 col-sm-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('accounting-account-types.update', $group) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Nombre --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nombre')) !!}*</label>
						<div class="col-lg-6 position-relative">
							<x-input type="text" name="name" placeholder="{!! ucfirst(trans('textos.nombre')) !!}" maxlength="150" value="{{ $group->name }}" required/>
						</div> 
					</div>
		
					<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection
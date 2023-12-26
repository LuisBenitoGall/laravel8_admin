@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		<a href="{{ route('remittance-patterns.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_patrones') !!}
		</a>

		{{-- Nuevo patrón --}}
		@can('remittance-patterns.create')
			<a class="btn btn-info" href="{{ route('remittance-patterns.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.patron_nuevo')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('remittance-patterns.update', $pattern) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Patrón --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.patron')) !!} *</label>
						<div class="col-lg-6">
							<x-input type="text" name="name" autocomplete="off" maxlength="150" required value="{{ $pattern->name }}"/>
						</div>

						{{-- Estado --}}
						<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.patron_activo')) !!}</label>
						<div class="col-lg-2">
							<label class="custom-chk-rdb" rel="tooltip" title="{!! trans('textos.patron_on_off') !!}">
								<input type="checkbox" name="status" @if($pattern->status == 1) checked @endif>
								<span class="chk" style="top: 5px;"></span>
							</label>
						</div>
					</div>
			
					<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection
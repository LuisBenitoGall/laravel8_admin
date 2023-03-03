@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		<a href="{{ route('payment-methods.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_metodos_pago') !!}
		</a>

		@can('payment-methods.create')
		    <a href={{ route('payment-methods.create') }} class="btn btn-info"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.metodo_nuevo')) !!}</a>
        @endcan
	</div>
</div>

<div class="row mb-3">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('payment-methods.update', $method) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Título método --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.metodo')) !!}*</label>
						<div class="col-lg-7">
							<x-input type="text" name="name" id="name" autocomplete="off" maxlength="255" value="{{ $method->name }}" required/>
						</div>

						{{-- Status --}}
						<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.estado')) !!}</label>
						<div class="col-lg-1">
							<label class="custom-chk-rdb" rel="tooltip" title="{!! trans('textos.banco_on_off') !!}">
	                            <input type="checkbox" name="status" @if($method->status == 1) checked @endif>
	                            <span class="chk" style="top: 5px;"></span>
	                        </label>
						</div>
					</div>

					<div class="form-group row">
						{{-- Descripción --}}
						<label for="description" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.descripcion')) !!}</label>
						<div class="col-lg-10">
							<x-input-textarea name="description" value="{{ $method->description }}"/>
						</div>
					</div>

					<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection
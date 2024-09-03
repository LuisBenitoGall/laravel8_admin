@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Listado agencias de transporte --}}
		<a href="{{ route('transport-agencies.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_agencias_transportes') !!}
		</a>

		{{-- Nuevo banco --}}
		@can('transport-agencies.create')
			<a class="btn btn-info" href="{{ route('transport-agencies.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.agencia_nueva')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-4">{{ ucfirst(trans('textos.agencia')) }} <u>{{ ucfirst($company->name) }}</u></h3>

				<form method="post" autocomplete="off" action="{{ route('transport-agencies.update', $agency) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Razón social de la agencia --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.agencia')) !!}*</label>
						<div class="col-lg-4">
							<x-input type="text" name="name" placeholder="{!! ucfirst(trans('textos.banco')) !!}" autocomplete="off" maxlength="150" required value="{{ $company->name }}"/>
						</div>	

						{{-- Nombre comercial --}}
						<label for="tradename" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nombre_comercial')) !!}</label>
						<div class="col-lg-4">
							<x-input type="text" name="tradename" placeholder="{!! ucfirst(trans('textos.nombre_comercial')) !!}" autocomplete="off" maxlength="150" value="{{ $company->tradename }}"/>
						</div>
					</div>

					<div class="form-group row">
						
                        {{-- Status --}}
						<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.estado')) !!}</label>
						<div class="col-lg-2">
							<label class="custom-chk-rdb" rel="tooltip" title="{!! trans('textos.banco_on_off') !!}">
	                            <input type="checkbox" name="status" @if($agency->status) checked @endif>
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

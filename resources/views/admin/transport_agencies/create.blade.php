@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		<a href="{{ route('transport-agencies.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_agencias_transportes') !!}
		</a>
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('transport-agencies.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}
					<input type="hidden" name="environment" value="true">
					<input type="hidden" name="guardian" value="false">
					<input type="hidden" name="business_relationship" value="providers">

					<div class="form-group row">
						{{-- Razón social de la agencia --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.agencia')) !!}*</label>
						<div class="col-lg-4">
							<x-input type="text" name="name" placeholder="{!! ucfirst(trans('textos.agencia')) !!}" autocomplete="off" maxlength="150" required info="company-name"/>
						</div>	

						{{-- Nombre comercial --}}
						<label for="tradename" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nombre_comercial')) !!}</label>
						<div class="col-lg-4">
							<x-input type="text" name="tradename" placeholder="{!! ucfirst(trans('textos.nombre_comercial')) !!}" autocomplete="off" maxlength="150" info="company-tradename"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- NIF --}}
						<label for="nif" class="col-md-2 col-form-label text-md-right">NIF *</label>
                        <div class="col-md-4">
                        	@php
                        	$is_invalid = '';
                        	@endphp
                        	@error('nif')
                        		@php
                        		$is_invalid = 'is-invalid';
                        		@endphp
                        	@enderror

                        	<x-input type="text" name="nif" class="{{ $is_invalid }}" placeholder="NIF" autocomplete="off" maxlength="16" required info="company-nif"/>
                        </div>

                        {{-- Status --}}
						<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.estado')) !!}</label>
						<div class="col-lg-2">
							<label class="custom-chk-rdb" rel="tooltip" title="{!! trans('textos.banco_on_off') !!}">
	                            <input type="checkbox" name="status" checked>
	                            <span class="chk" style="top: 5px;"></span>
	                        </label>
	                    </div>
                    </div>

                    

					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection

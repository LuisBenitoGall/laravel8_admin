@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Volver a SATs --}}
		<a href="{{ route('sat.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_sat') !!}
		</a>	

	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('sat.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="row">
						{{-- Buscar cliente --}}
						<div class="col-md-6">
							<h3 class="text-center mb-5">{{ trans('textos.cliente_buscar') }}</h3>

							<div class="form-group row">
								{{-- Cliente --}}
								<label for="company" class="col-lg-4 col-form-label">{!! ucfirst(trans('textos.cliente')) !!}</label>
								<div class="col-lg-8 position-relative">
									<x-input type="text" name="company" id="search_company" placeholder="{!! ucfirst(trans('textos.cliente')) !!}" maxlength="150"/>
								</div>

								<input type="hidden" name="company_id" id="companyId">
							</div>

							<div class="form-group row" id="company-nif" style="display: none;">
								{{-- NIF --}}
								<label for="nif" class="col-lg-4 col-form-label">NIF</label>
								<div class="col-lg-8 position-relative">
									<x-input type="text" name="nif" id="companyNif" maxlength="15" readonly/>
								</div>
							</div>
						</div>

						{{-- Nuevo cliente --}}
						<div class="col-md-6" style="border-left: 1px solid #ddd;">
							<h3 class="text-center mb-5">{{ ucfirst(trans('textos.cliente_nuevo')) }}</h3>

							<div class="form-group row">
								{{-- Nombre --}}
								<label for="name" class="col-lg-4 col-form-label">{!! ucfirst(trans('textos.nombre')) !!}</label>
								<div class="col-lg-8">
									<x-input type="text" name="name" placeholder="{!! ucfirst(trans('textos.nombre')) !!}" autocomplete="off" maxlength="150"/>
								</div>
							</div>

							<div class="form-group row">
								{{-- Apellidos --}}
								<label for="surname" class="col-lg-4 col-form-label">{!! ucfirst(trans('textos.apellidos')) !!}</label>
								<div class="col-lg-8">
									<x-input type="text" name="surname" placeholder="{!! ucfirst(trans('textos.apellidos')) !!}" autocomplete="off" maxlength="150"/>
								</div>
							</div>
						</div>
					</div>

					<x-btn-submit name="submit_save" classdiv="col-12 text-right" label="{{ trans('textos.continuar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection
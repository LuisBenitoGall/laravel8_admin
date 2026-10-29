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
								<label for="company_" class="col-lg-4 col-form-label">{!! ucfirst(trans('textos.cliente')) !!}</label>
								<div class="col-lg-8 position-relative">
									<x-input type="text" name="company_" id="search_company" placeholder="{!! ucfirst(trans('textos.cliente')) !!}" maxlength="150"/>
								</div>

								<input type="hidden" name="company_id" id="companyId">
							</div>

							<div class="form-group row" id="company-nif" style="display: none;">
								{{-- NIF --}}
								<label for="nif_" class="col-lg-4 col-form-label">NIF</label>
								<div class="col-lg-8 position-relative">
									<x-input type="text" name="nif_" id="companyNif" maxlength="16" readonly/>
								</div>
							</div>
						</div>

						{{-- Nuevo cliente --}}
						<div class="col-md-6" style="border-left: 1px solid #ddd;">
							<h3 class="text-center mb-5">{{ ucfirst(trans('textos.cliente_nuevo')) }}</h3>

							<div class="form-group row">
								{{-- Tipo de cliente --}}
								<label for="type" class="col-lg-4 col-form-label">{!! ucfirst(trans('textos.cliente_tipo')) !!}</label>
								<div class="col-lg-8">
									<label class="custom-chk-rdb mr-5" style="margin-bottom: 0; margin-top: 7px;">
						        		<input type="radio" name="type" value="c" class="chkType">
						        		<span class="rdb chksmall"></span>
						        		{{ ucfirst(trans('textos.empresa')) }}
						        	</label>

						        	<label class="custom-chk-rdb" style="margin-bottom: 0; margin-top: 7px;">
						        		<input type="radio" name="type" value="u" class="chkType">
						        		<span class="rdb chksmall"></span>
						        		{{ trans('textos.particular') }}
						        	</label>
						        </div>
							</div>

							<div class="form-group row inputCompany" style="display: none;">
								<label for="company" class="col-lg-4 col-form-label">{!! ucfirst(trans('textos.empresa')) !!}</label>
								<div class="col-lg-8 position-relative">
									<x-input type="text" name="company" placeholder="{!! ucfirst(trans('textos.empresa')) !!}" maxlength="150"/>
								</div>
							</div>

							<div class="form-group row inputUser" style="display: none;">
								{{-- Nombre --}}
								<label for="name" class="col-lg-4 col-form-label">{!! ucfirst(trans('textos.nombre')) !!}</label>
								<div class="col-lg-8">
									<x-input type="text" name="name" placeholder="{!! ucfirst(trans('textos.nombre')) !!}" autocomplete="off" maxlength="150"/>
								</div>
							</div>

							<div class="form-group row inputUser" style="display: none;">
								{{-- Apellidos --}}
								<label for="surname" class="col-lg-4 col-form-label">{!! ucfirst(trans('textos.apellidos')) !!}</label>
								<div class="col-lg-8">
									<x-input type="text" name="surname" placeholder="{!! ucfirst(trans('textos.apellidos')) !!}" autocomplete="off" maxlength="150"/>
								</div>
							</div>

							<div class="form-group row inputUser" style="display: none;">
								{{-- Email --}}
								<label for="email" class="col-lg-4 col-form-label">{!! ucfirst(trans('textos.email')) !!}</label>
								<div class="col-lg-8">
									<x-input type="email" name="email" placeholder="{!! ucfirst(trans('textos.email')) !!}" autocomplete="off" maxlength="255"/>
								</div>
							</div>

							<div class="form-group row inputNif" style="display: none;">
								{{-- NIF --}}
								<label for="nif" class="col-lg-4 col-form-label">NIF</label>
								<div class="col-lg-8">
									<x-input type="text" name="nif" placeholder="NIF" autocomplete="off" maxlength="16"/>
								</div>
							</div>

							@if($errors->any())
			        			<ul class="errors">
			        	    		@foreach ($errors->all() as $error)
        								<li>{{ $error }}</li>
    								@endforeach
			        			</ul>
			    			@endif
						</div>
					</div>

					<x-btn-submit name="submit_save" classdiv="col-12 text-right" label="{{ trans('textos.continuar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tipo de cliente:
	$('.chkType').on('click', function(){
		var type = $(this).val();
		if(type == 'c'){
			$('.inputCompany, .inputNif').slideDown();
			$('.inputUser').slideUp();
			$('input[name="name"], input[name="surname"], input[name="email"], input[name="nif"]').val('');
		}else if(type == 'u'){
			$('.inputUser, .inputNif').slideDown();
			$('.inputCompany').slideUp();
			$('input[name="company"], input[name="nif"]').val('');	
		}
	});
});
</script>
@endpush

@endsection
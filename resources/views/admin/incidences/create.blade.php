@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('incidences.index')
			<a href="{{ route('incidences.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_incidencias') !!}
			</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('incidences.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}
					<input type="hidden" name="origin" value="1">

					<div class="form-group row">
						{{-- Fecha incidencia --}}
						<label for="incidence_date" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.fecha')) !!}*</label>
						<div class="col-lg-2">
							<x-date-picker name="incidence_date" :format="$languages[config('app.locale')][4]" required value="{{ date('d/m/Y') }}"/>
						</div>	

						{{-- Concepto --}}
						<label for="concept" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.concepto')) !!}*</label>
						<div class="col-lg-6">
							<x-input type="text" name="concept" maxlength="255" placeholder="{!! ucfirst(trans('textos.concepto')) !!}" autocomplete="off" required/>
						</div>	
					</div>

					<div class="form-group row">
						<p class="col-12 text-center font-weight-bold text-warning mt-3">
							{{ trans('textos.incidencia_selec_empresa_usuario') }}
						</p>

						{{-- Empresa --}}
						<label for="customer" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.empresa')) !!}</label>
						<div class="col-lg-4 position-relative">
							<x-input type="text" name="customer" id="search_company" placeholder="{!! ucfirst(trans('textos.empresa')) !!}" maxlength="150"/>

							<x-input-errors name="customer_id"/>
						</div>

						<input type="hidden" name="customer_id" id="companyId">

						{{-- Usuario --}}
						<label for="subject" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.usuario')) !!}</label>
						<div class="col-lg-4 position-relative">
							<x-input type="text" name="subject" id="search_user" placeholder="{!! ucfirst(trans('textos.usuario')) !!}" maxlength="150"/>

							<x-input-errors name="subject_id"/>
						</div>

						<input type="hidden" name="subject_id" id="userId">
					</div>

					<div class="form-group row">
						{{-- Descripción --}}
                    	<label for="observations" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.descripcion')) !!}</label>
						<div class="col-lg-10">
							<x-input-textarea name="observations" class="editor"/>
						</div>
					</div>

					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection
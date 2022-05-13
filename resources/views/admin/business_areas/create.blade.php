@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-sm-12">
		{{-- Acciones: --}}
		<a href="{{ route('business-areas.index', [$company, $business_relationship]) }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_areas_negocio') !!}
		</a>
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-4">{{ trans('textos.area_negocio_nueva_de') }} <u>{{ ucfirst($company->name) }}</u></h3>

				<form method="post" autocomplete="off" action="{{ route('business-areas.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}
					<input type="hidden" name="company_id" value="{{ $company->id }}">
					<input type="hidden" name="business_relationship" value="{{ $business_relationship }}">

					<div class="form-group row">
						{{-- Area de negocio --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nombre')) !!}*</label>
						<div class="col-lg-6">
							<x-input type="text" name="name" id="name" autocomplete="off" maxlength="150" required/>
						</div>

						{{-- Status --}}
						<label for="status" class="col-lg-2 col-form-label">{!! trans('textos.area_activo') !!}</label>
						<div class="col-lg-2">
							<label class="custom-chk-rdb" rel="tooltip" title="{!! trans('textos.area_negocio_on_off') !!}">
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

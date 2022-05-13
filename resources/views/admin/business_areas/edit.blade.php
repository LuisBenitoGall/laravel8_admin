@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-sm-12">
		<a href="{{ route('business-areas.index', [$business_area->company_id, $business_relationship]) }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_areas_negocio') !!}
		</a>

		@can('companies.edit')
			<a class="btn btn-info" href="{{ route('business-areas.create', [$business_area->company_id, $business_relationship]) }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.area_negocio_nueva')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-4">{{ trans('textos.area_negocio_editar_de') }} <u>{{ ucfirst($company->name) }}</u></h3>

				<form method="post" autocomplete="off" action="{{ route('business-areas.update', $business_area->id) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}
					<input type="hidden" name="id" value="{{ $business_area->id }}">
					<input type="hidden" name="business_relationship" value="{{ $business_relationship }}">

					<div class="form-group row">
						{{-- Nombre área de negocio --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nombre')) !!}*</label>
						<div class="col-lg-6">
							<x-input type="text" name="name" id="name" value="{{ $business_area->name }}" autocomplete="off" maxlength="150" required/>
						</div>

						{{-- Status --}}
						<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.area_activo')) !!}</label>
						<div class="col-lg-2">
							<label class="custom-chk-rdb" rel="tooltip" title="{!! trans('textos.area_negocio_on_off') !!}">
	                            <input type="checkbox" name="status" @if($business_area->status == 1) checked @endif>
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

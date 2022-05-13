@extends ('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-sm-12">
		<a href="{{ route('cost-centers.index', [$cost_center->company_id, $business_relationship]) }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_centros_coste') !!}
		</a>

		@can('companies.edit')
			<a class="btn btn-info" href="{{ route('cost-centers.create', [$cost_center->company_id, $business_relationship]) }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.centro_coste_nuevo')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-4">{{ trans('textos.centro_coste_editar_de') }} <u>{{ ucfirst($company->name) }}</u></h3>

				<form method="post" autocomplete="off" action="{{ route('cost-centers.update', $cost_center->id) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}
					<input type="hidden" name="id" value="{{ $cost_center->id }}">
					<input type="hidden" name="business_relationship" value="{{ $business_relationship }}">

					<div class="form-group row">
						{{-- Centro de coste --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nombre')) !!}*</label>
						<div class="col-lg-6">
							<x-input type="text" name="name" id="name" autocomplete="off" maxlength="150" required value="{{ $cost_center->name }}"/>
						</div>

						{{-- Estado --}}
						<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.centro_activo')) !!}</label>
						<div class="col-lg-2">
							<label class="custom-chk-rdb" rel="tooltip" title="{!! trans('textos.centro_coste_on_off') !!}">
	                            <input type="checkbox" name="status" @if($cost_center->status == 1) checked @endif>
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

@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-sm-12">
		<a href="{{ route('companies.edit', session('currentCompany')) }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_edicion_empresa') !!}
		</a>
	</div>
</div>

<div class="row mb-3">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">	
				<p class="text-warning mb-4">{{ trans('textos.notificaciones_gestion_texto') }}</p>

				<form method="post" autocomplete="off" action="{{ route('company-notifications-management.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						@foreach($modules as $module)
							@php
							//Obtenemos usuarios habilitados para el módulo:
							$users = App\Models\CompanyNotificationsManagement::getCompanyModuleUsers(session('currentCompany'), $module->id, true);
							@endphp

							<div class="col-12 col-sm-6 col-md-4 mb-3">
								<label style="margin-bottom: 2px;">{{ ucfirst(trans('textos.'.$module->label)) }}</label>

								<x-select name="{{ $module->slug }}[]" class="select2" multiple="true" :options="$employees" :values="$users"/>
							</div>
						@endforeach
					</div>

					<x-btn-submit name="submit_store" label="{{ trans('textos.guardar') }}"/>	
				</form>
			</div>
		</div>
	</div>
</div>

@endsection
@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="dtCompanyId" value="{{ $company->id }}">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-sm-12">
		<a href="{{ route('companies.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_empresas') !!}
		</a>

		@include('admin.companies.partials.actions', ['company' => $company, 'business_relationship' => $business_relationship])

		{{-- UTE --}}
		@if($company->is_ute)
			<a href="{{ route('company-utes.index', $company->id) }}" class="btn btn-info">
				<i class="fa fa-object-group"></i>
				{!! trans('textos.ute_empresas_vinculadas') !!}
			</a>
		@endif

		{{-- Asociar a empresa matriz - condiciones: 
			1. id y mother_co deben coincidir.
			2. No deben ser UTEs.
			...
		--}}
		@if($company->id == $company->mother_co && !$company->is_ute)
			<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-link-mother">
				<i class="fa fa-building"></i>
				{!! trans('textos.empresa_matriz_vincular') !!}
			</a>
		@endif

		{{-- Desvincular de empresa matriz - condiciones: 
			1. id y mother_co NO deben coincidir.
			2. No deben ser UTEs. 
		--}}
		@if($company->id != $company->mother_co && !$company->is_ute)
			<a href="{{ route('companies.unlink-mother', $company) }}" class="btn btn-danger btn-confirm" data-msg="{{ trans('textos.empresa_matriz_desvincular_confirm') }}">
				<i class="fa fa-unlink"></i>
				{!! trans('textos.empresa_matriz_desvincular') !!}
			</a>
		@endif

		{{-- Gestión de notificaciones: sólo para la empresa en session --}}
		@if(session('currentCompany') == $company->id)
			<a href="{{ route('company-notifications-management.index') }}" class="btn btn-info">
				<i class="fa fa-bell"></i>
				{!! trans('textos.notificaciones_gestion') !!}
			</a>
		@endif
	</div>
</div>

<div class="row mb-3">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">	
				<h3 class="mb-3">
					<u>{{ ucfirst($company->name) }}</u>
					@if($mother)
						- {{ trans('textos.empresa_matriz') }} 
						<a target="_blank" href="{{ route('companies.edit', $mother->id) }}"><u>{{ $mother->name }}</u></a>
					@endif
				</h3>	

				{{-- Info --}}
				<div class="mb-3" style="width: 90%;">
					{{-- Fecha alta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.fecha_alta')) !!}: 
						<b>
							{{ $company->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>
				</div>

				{{-- Tabs: --}}
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item @if(!$tab) active @endif">
			    		<a class="nav-link @if(!$tab) active @endif" href="#tab-info" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.informacion')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'sectors') active @endif">
			  			<a class="nav-link @if($tab == 'sectors') active @endif" href="#tab-sectors" role="tab" data-toggle="tab">{{ ucfirst(trans('textos.sectores')) }}</a>	
			  		</li>
			  	</ul>

			  	<div class="tab-content pt-4">
					{{-- Cabecera --}}
					<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-info">
						@include('admin.companies.partials.company-info')	
					</div>

					{{-- Sectores --}}
					<div class="tab-pane fade @if($tab == 'sectors') show active @endif" role="tabpanel" id="tab-sectors">
						@include('admin.companies.partials.company-sectors')	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

{{-- Modals --}}
{{-- Vincular empresa a matriz --}}
@include('admin.companies.partials.modal-link-mother')

@push('scripts')
<script>
$(function(){
	//Vincular / desvincular sectores a empresa.
    // $(document).on('change', '.btnCompanySector', function(){
    // 	var elem = $(this);
    //     var company_id = $('#dtCompanyId').val();
    //     var category_id = elem.find('.chk').data('id');
    //     $.get("/admin/company-sectors/store/"+company_id+"/"+category_id+"", function(response, state){
    //         if(response){
    //             $('.check-ko').remove();
    //             elem.before('<i class="fa fa-check check-ok"></i>');
    //             $('.check-ok').delay(1000).fadeOut();
    //         }else{
    //             elem.before('<i class="fa fa-exclamation-triangle check-ko"></i>');
    //         }
    //     });  
    // });
});
</script>
@endpush

@endsection
@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		@can('providers.edit')
			<a href="{{ route('product-providers.index', $product) }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_proveedores') !!}
			</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				{{-- Condiciones necesarias --}}
				{{-- 1. Activada gestión de proveedores: --}}
				@if(!$providers)
					<div class="row">
						<label class="col-12 text-center text-warning mt-4">
							<b>{!! ucfirst(trans('textos.proveedores_gestion_off')) !!}</b> 
							<a href="{{ route('company-settings.index') }}" class="btn btn-info ml-4">{!! ucfirst(trans('textos.configuracion')) !!}</a>
						</label>
					</div>	

				{{-- 3. Debe haber proveedores --}}
				@elseif($providers && !$providers->count())
					<div class="form-group row">
						<label class="col-12 text-center text-warning mt-4">
							<b>{!! ucfirst(trans('textos.proveedores_no_hay')) !!}</b> 
							<a href="{{ route('providers.index') }}" class="btn btn-info ml-4">{!! ucfirst(trans('textos.proveedores')) !!}</a>
						</label>
					</div>	

				{{-- 3. Se cumplen las condiciones --}}
				@else
					<form method="post" autocomplete="off" action="{{ route('product-providers.set-provider', $product) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
						{{ csrf_field() }}

						{{-- Selecciona proveedor --}}
						<div class="form-group row">
							<label for="provider_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.proveedor')) !!} *</label>
							<div class="col-lg-6">
								@php
								$providers_ = [];

								$prov = $provider? $provider->id:'';
								@endphp

								@foreach($providers as $pvd)
									@php
									$company = $pvd->tradename? $pvd->name.' ('.$pvd->tradename.')':$pvd->name;
									$providers_[$pvd->id] = $company;
									@endphp
								@endforeach

								<x-select name="provider_id" class="select2" label="selec_proveedor" :options="$providers_" value="{{ $prov }}" required/>
							</div>

							<x-btn-submit name="submit_save" classdiv="col-sm-10 text-right" label="{{ trans('textos.continuar') }}" inline="true"/>
						</div>
					</form>
				@endif

				@if($provider)
					<br><br>
					@livewire('products-grid', ['currency' => $currency, 'order_' => false, 'company' => $provider, 'customer' => session('currentCompany'), 'sale' => true])
				@endif

			</div>
		</div>
	</div>
</div>

@endsection		
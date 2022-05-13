@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('budgets.index')
			<a href="{{ route('budgets.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_presupuestos') !!}
			</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				{{-- Condiciones necesarias --}}
				{{-- 1. Activada gestión de clientes: --}}
				@if(!$customers)
					<div class="row">
						<label class="col-12 text-center text-warning mt-4">
							<b>{!! ucfirst(trans('textos.clientes_gestion_off')) !!}</b> 
							<a href="{{ route('company-settings.index') }}" class="btn btn-info ml-4">{!! ucfirst(trans('textos.configuracion')) !!}</a>
						</label>
					</div>				

				{{-- 2. Activo el módulo de productos --}}
				@elseif(!$module_products)
					<div class="form-group row">
						<label class="col-12 text-center text-warning mt-4">
							<b>{!! ucfirst(trans('textos.modulo_catalogo_activar')) !!}</b> 
							<a href="{{ route('company-modules.index') }}" class="btn btn-info ml-4">{!! ucfirst(trans('textos.modulo_activar')) !!}</a>
						</label>
					</div>		

				{{-- 3. Debe haber clientes --}}
				@elseif($customers && $customers->count() == 0)
					<div class="form-group row">
						<label class="col-12 text-center text-warning mt-4">
							<b>{!! ucfirst(trans('textos.clientes_no_hay')) !!}</b> 
							<a href="{{ route('customers.index') }}" class="btn btn-info ml-4">{!! ucfirst(trans('textos.clientes')) !!}</a>
						</label>
					</div>

				{{-- 4. Se cumplen las condiciones --}}
				@else
					<form method="post" autocomplete="off" action="{{ route('budgets.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
						{{ csrf_field() }}
						<input type="hidden" id="numForms" value="{{ $forms->count() }}">

						{{-- Selecciona cliente --}}
						<div class="form-group row">
							<label for="customer_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cliente')) !!} *</label>
							<div class="col-lg-8">
								@php
								$customers_ = [];
								@endphp

								@foreach($customers as $ctm)
									@php
									$company = $ctm->tradename? $ctm->name.' ('.$ctm->tradename.')':$ctm->name;
									$customers_[$ctm->id] = $company;
									@endphp
								@endforeach

								<x-select name="customer_id" id="formCustomerId" class="select2" label="selec_cliente" :options="$customers_" required/>
							</div>
						</div>

						{{-- Selecciona modalidad de presupuesto --}}
						@if($customers && $customers->count() > 0)
							<div class="form-group row">
								<label for="modality" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.modalidad')) !!} *</label>
								<div class="col-lg-8">
									@php
									$modalities = App\Helpers\Helpers::budgetModalities();
									@endphp

									<x-select name="modality" id="selectBudgetModality" class="select2" label="modalidad_selec" :options="$modalities" info="budget-modality" required/>
								</div>
							</div>
						@endif

						{{-- Mensaje: No hay formularios --}}
						<div id="noBudgetForms">
							<div class="form-group row">
								<label class="col-12 text-center text-warning mt-4"><b>{!! ucfirst(trans('textos.formularios_no_hay')) !!}</b> <a href="{{ route('budget-forms.index') }}" class="btn btn-info ml-4">{!! ucfirst(trans('textos.formulario_crear')) !!}</a></label>
							</div>
						</div>

						<div @if(!$errors->has('form_id')) id="budgetForms" @endif>
							{{-- Selecciona formulario --}}
							<div class="form-group row">
								<label for="form_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.formulario')) !!} *</label>
								<div class="col-lg-8">
									@php
									$forms_ = [];
									@endphp

									@foreach($forms as $form)
										@php
										$forms_[$form->id] = $form->name;
										@endphp
									@endforeach

									<x-select name="form_id" id="budgetForm" class="select2" label="formulario_selec" :options="$forms_"/>
								</div>
							</div>
						</div>
							
						<x-btn-submit name="submit_save" id="sbtNewBudget" classdiv="col-sm-10 text-right" label="{{ trans('textos.continuar') }}"/>
					</form>
				@endif
			</div>
		</div>
	</div>
</div>

@endsection
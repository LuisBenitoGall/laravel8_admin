@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Listado de gastos --}}
		<a href="{{ route('expenses.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_gastos') !!}
		</a>

		{{-- Nuevo gasto --}}
		@can('expenses.create')
    		<a href="{{ route('expenses.create') }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.gasto_nuevo')) !!}
    		</a>
        @endcan
	</div>
</div>

<div class="row mb-3">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('expenses.update', $expense) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Concepto --}}
						<label for="concept" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.concepto')) !!}*
						</label>
						<div class="col-lg-6">
							<x-input type="text" name="concept" placeholder="{!! ucfirst(trans('textos.concepto')) !!}" autocomplete="off" maxlength="155" value="{{ $expense->concept }}" required/>
						</div>

						{{-- Fecha --}}
						<label for="expensed_in" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.fecha')) !!}</label>
						<div class="col-lg-2">
							@php
							$expensed_in = $expense->expensed_in && $expense->expensed_in > 0? \Carbon\Carbon::parse($expense->expensed_in)->format($languages[session()->get('locale')][4]):'';
							@endphp

							<x-date-picker name="expensed_in" id="expensed_in" :format="$languages[config('app.locale')][4]" value="{{ $expensed_in }}"/>

							{!! App\Helpers\Helpers::info('expense-date') !!}
						</div>
					</div>

					<div class="form-group row">
						{{-- Importe (sin iva) --}}
						<label for="amount" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.importe')) !!} <small class="text-warning">({{ trans('textos.iva_sin') }})</small></label>
						<div class="col-lg-2">
							<x-input type="text" name="amount" class="text-right set-decimals" id="expenseAmount" autocomplete="off" maxlength="11" group="right" groupvalue="{{ $currency->symbol }}" info="amount-no-iva" value="{{ $expense->amount }}"/>
						</div>

						{{-- IVA --}}
						<label for="iva" class="col-lg-1 col-form-label">IVA</label>
						<div class="col-lg-1">
							<x-input type="text" name="iva" id="expenseIva" class="text-right set-decimals" value="{{ $iva }}" autocomplete="off" maxlength="5" group="right" groupvalue="%" value="{{ $expense->iva }}"/>
						</div>

						{{-- Importe (con iva) --}}
						<label for="amount_with_iva" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.importe_total')) !!} <small class="text-warning">({{ trans('textos.iva_incluye') }})</small></label>
						<div class="col-lg-2">
							<x-input type="text" name="amount_with_iva" class="text-right set-decimals" id="expenseAmount_with_iva" autocomplete="off" maxlength="11" group="right" groupvalue="{{ $currency->symbol }}" info="amount-total" value="{{ $expense->amount_with_iva }}"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Tipo de gasto --}}
						<label for="type" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.gasto_tipo')) !!}</label>
						<div class="col-lg-2">
							<x-select name="type" class="select2" label="gasto_tipo" :options="$types" info="expense-type" value="{{ $expense->type }}"/>
						</div>

						{{-- Proyectos --}}
						<label for="project_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.proyectos')) !!}</label>
						<div class="col-lg-4">
							<x-select name="project_id" class="select2" label="proyecto_selec" :options="$projects" info="expense-project" value="{{ $expense->project_id }}"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Proveedor --}}
						<label for="provider_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.proveedor')) !!}</label>
						<div class="col-lg-3">
							<x-select name="provider_id" class="select2" id="expenseProvider" label="proveedor_selec" :options="$providers" info="expense-provider" value="{{ $expense->provider_id }}"/>
						</div>

						{{-- Añadir nuevo proveedor --}}
						<div class="col-md-1 text-center pt-1">
							<a href="#" class="btn btn-warning btn-circle" data-toggle="modal" data-target="#modal-add-provider" rel="tooltip" title="{!! trans('textos.proveedor_nuevo_add') !!}">
								<i class="fa fa-plus"></i>
							</a>
						</div>

						{{-- Documento --}}
						<label for="doc" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.documento')) !!}</label>
						<div class="col-lg-3">
							@if($expense->doc)
								{{-- Ver documento --}}
								<a target="_blank" class="btn btn-info" href="{!! asset('docs/expenses/'.$expense->doc) !!}" rel="tooltip" title="{{ trans('textos.documento_ver') }}"><i class="fa fa-file"></i></a>		
								{{-- Eliminar documento --}}
								<a target="_blank" class="btn btn-danger ml-3 btn-confirm" href="{{ route('expenses.destroy-doc', $expense) }}" rel="tooltip" title="{{ trans('textos.documento_eliminar') }}" data-msg="{{ trans('textos.documento_eliminar_confirm') }}"><i class="fa fa-trash"></i></a>
							@else
								<x-input-file name="doc"/>

								@if($errors->has('doc'))
				                    <span class="invalid-feedback" role="alert">
				                      <strong>{{ $errors->first('doc') }}</strong>
				                    </span>
				                @endif
				            @endif
						</div>
					</div>

					<div class="form-group row">
						{{-- Observaciones --}}
						<label for="observations" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.observaciones')) !!}</label>
						<div class="col-lg-10">
							<x-input-textarea name="observations" class="editor" value="{{ $expense->observations }}"/>
						</div>
					</div>

					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>	
	</div>
</div>

{{-- Modals --}}
{{-- Add provider --}}
@include('admin.expenses.partials.modal-add-provider')

@endsection
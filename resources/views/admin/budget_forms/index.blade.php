@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('budget-forms.create')
    		<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-new-form">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.formulario_nuevo')) !!}
    		</a>
        @endcan
    </div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblBudgetForms">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.formulario')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
                	<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

{{-- Modals: --}}
{{-- New form: --}}
@include('admin.budget_forms.partials.modal-new-form')

@endsection

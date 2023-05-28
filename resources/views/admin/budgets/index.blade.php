@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Nuevo presupuesto --}}
		@can('budgets.create')
			@if($budget_from == 'customers')
				<a href="{{ route('budgets.create') }}" class="btn btn-info">
					<i class="fa fa-plus"></i>
					{!! ucfirst(trans('textos.presupuesto_nuevo')) !!}
				</a>
			@endif
		@endcan
	</div>
</div>

<input type="hidden" id="dtFrom" value="{{ $budget_from }}">
<input type="hidden" id="dtController" value="budgets">

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblBudgets">
			<thead>
				<tr>
					<th class="text-center wdt100">#</th>
					{{-- 07/12/22: se oculta esta columna a petición de Cuentas --}}
					{{-- <th class="text-center wdt100">{!! ucfirst(trans('textos.numero_antiguo')) !!}</th> --}}
					<th class="wdt150">{!! ucfirst(trans('textos.presupuesto')) !!}</th>
					<th class="wdt100">{!! ucfirst(trans('textos.pedido')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.importe')) !!}</th>
					{{-- <th class="wdt100">{!! ucfirst(trans('textos.modalidad')) !!}</th> --}}
					<th class="wdt200">
						@if($budget_from == 'customers')
							{!! ucfirst(trans('textos.responsable')) !!}
						@else
							{!! ucfirst(trans('textos.comprador')) !!}
						@endif
					</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
					<th class="wdt100">
						@if($budget_from == 'customers')
							{!! ucfirst(trans('textos.cliente')) !!}
						@else
							{!! ucfirst(trans('textos.proveedor')) !!}
						@endif
					</th>
					<th class="wdt200">
						{!! ucfirst(trans('textos.cliente_final')) !!}
					</th>
					<th class="wdt100">{!! ucfirst(trans('textos.estado')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@endsection
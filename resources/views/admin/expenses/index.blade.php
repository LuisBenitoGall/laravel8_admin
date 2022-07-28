@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
        {{-- Nuevo gasto --}}
		@can('expenses.create')
    		<a href="{{ route('expenses.create') }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.gasto_nuevo')) !!}
    		</a>
        @endcan
	</div>
</div>

<input type="hidden" id="dtController" value="expenses">

{{-- Filtros --}}
<div class="row mb-3">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">


			</div>
		</div>
	</div>
</div>

{{-- Tabla --}}
<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblExpenses">
			<thead>
				<tr>
					<th class="text-center wdt150">{!! ucfirst(trans('textos.concepto')) !!}</th>
					<th class="text-center wdt50">{!! ucfirst(trans('textos.fecha')) !!}</th>
					<th class="text-center wdt100">{!! trans('textos.importe') !!}</th>
					<th class="wdt100">{!! trans('textos.gasto_tipo') !!}</th>
					<th class="wdt100">{!! ucfirst(trans('textos.proveedor')) !!}</th>
					<th class="text-center wdt150">{!! ucfirst(trans('textos.proyecto')) !!}</th>
					<th class="text-center wdt150">{!! ucfirst(trans('textos.documento')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>

		</table>
	</div>
</div>

@endsection
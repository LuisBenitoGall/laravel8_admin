@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="dtYear" value="{{ $year }}">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		@can('orders.index')
			<a href="{{ route('orders.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_pedidos') !!}
			</a>
		@endcan
	</div>
</div>

{{-- Filtro --}}
<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('orders.control-panel-sales-year') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}
					<input type="hidden" name="order_type" id="orderType" value="{{ $order_type }}">

					<div class="form-group row">
						{{-- Año --}}
						@include('admin.partials.selector-year', ['selected_year' => $year])

						<x-btn-submit name="submit_filter" label="{{ trans('textos.buscar') }}" inline/>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="table-responsive">

		</div>
	</div>
</div>

@endsection
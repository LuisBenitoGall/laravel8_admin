@extends ('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		@can('orders.index')
			<a href="{{ route('order-groups.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_agrupaciones') !!}
			</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('order-groups.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Nombre agrupación --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.agrupacion_pedidos')) !!} *</label>
						<div class="col-lg-4">
							<x-input type="text" name="name" autocomplete="off" maxlength="150" required info="order-group-name"/>
						</div>

						{{-- Selector clientes --}}
						@include('admin.partials.selector-customer', ['all' => false, 'customer' => false, 'required' => true, 'selected' => ''])
					</div>

					<div class="form-group row">
						{{-- Importe de la bolsa --}}
						<label for="bag_amount" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.importe_bolsa')) !!}</label>
						<div class="col-lg-2">
							<x-input type="text" name="bag_amount" class="text-right set-decimals" autocomplete="off" maxlength="15" group="right" groupvalue="{{ $currency->symbol }}" info="order-group-bag"/>
						</div>
					</div>

					<x-btn-submit name="submit_save" label="{{ trans('textos.crear') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection

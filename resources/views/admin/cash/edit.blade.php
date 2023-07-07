@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Volver a histórico de movimientos --}}
		<a href="{{ route('cash.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_caja_historico') !!}
		</a>

		{{-- Caja --}}
		<a href="{{ route('cash.create') }}" class="btn btn-info">
			<i class="fa fa-cash-register"></i>
			{!! ucfirst(trans('textos.caja_nueva')) !!}
		</a>

		{{-- Convertir en pedido - condiciones: 
			1. Disponer de permisos de generación de pedidos.
			...
		--}}
		@can('orders.create')
			<a href="{{ route('orders.order-from-cash', $cash) }}" class="btn btn-info">
				<i class="fa fa-shopping-cart"></i>
				{!! ucfirst(trans('textos.pedido_convertir')) !!}
			</a>	
		@endcan

		{{-- Eliminar caja --}}
		@can('cash.destroy')
        	<form method="POST" action="{{ route('cash.destroy', $cash) }}" class="frm-inline" role="form" accept-charset="UTF-8">
                <input name="_method" type="hidden" value="DELETE">
                {{ csrf_field() }}
                <button type="submit" class="btn btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.caja_eliminar')) }}" data-msg="{{ ucfirst(trans('textos.caja_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
            </form>
        @endcan
	</div>
</div>

<div class="row mb-3">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				@livewire('budget-amount', ['budget_amount' => $cash->cash_amount, 'currency' => $currency->symbol, 'budget_amount_without_discount' => '', 'environment' => 'orders', 'bag_amount' => false])

				<h3 class="mb-3">
					{!! ucfirst(trans('textos.caja_venta')) !!} 
					<u>{{ $cash->id }}</u>
				</h3>

				{{-- Info --}}
				<div class="mb-3" style="max-width: 95%;">
					{{-- Fecha alta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.fecha_alta')) !!}: 
						<b>
							{{ $cash->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Autor --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.autor')) !!}:
						<b>
							@if($author)
								{{ ucfirst($author->name) }} {{ ucfirst($author->surname) }}
							@endif
						</b>
					</label>
				</div>

				<form method="post" autocomplete="off" action="{{ route('cash.update', $cash) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Cliente --}}
						<label for="customer" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cliente')) !!}</label>
						<div class="col-lg-3 sinpadding-right">
							@php
							$customer_ = $customer? $customer->name:'';
							@endphp
							<x-input type="text" name="company_" id="search_company" placeholder="{!! ucfirst(trans('textos.cliente')) !!}" maxlength="150" value="{{ $customer_ }}"/>
						</div>

						<input type="hidden" name="company_id" id="companyId" value="{{ $cash->customer_id }}">

						{{-- Añadir nuevo cliente --}}
						<div class="col-md-1 text-center pt-1">
							<a href="#" class="btn btn-warning btn-circle" data-toggle="modal" data-target="#modal-add-company" rel="tooltip" title="{!! ucfirst(trans('textos.cliente_nuevo')) !!}">
								<i class="fa fa-plus"></i>
							</a>
						</div>

						{{-- Moneda --}}
						<label for="currency" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.moneda')) !!}*</label>
						<div class="col-lg-4">
							<x-select name="currency" class="select2" label="moneda_selec" :options="$currencies" value="{{ $cash->currency_id }}" required/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Métodos de pago --}}
						<label for="payment_method" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.metodo_pago')) !!}*</label>
						<div class="col-lg-4">
							@php
							$pm_id = $cash->payment_method_id? $cash->payment_method_id:'';
							@endphp
							<x-select name="payment_method" class="select2" label="metodo_selec" :options="$payment_methods" value="{{ $pm_id }}" required/>
						</div>

						{{-- Centro de trabajo --}}
						<label for="workplace" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.centro_trabajo')) !!}*</label>
						<div class="col-lg-4">
							@php
							$workplace_id = $cash->workplace_id? $cash->workplace_id:'';
							@endphp
							<x-select name="workplace" class="select2" label="centro_trabajo_selec" :options="$workplaces" value="{{ $workplace_id }}" required/>
						</div>
					</div>
					<hr class="my-4">

					<div class="form-group row">
						{{-- Producto --}}
						<label for="product_" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.producto')) !!}</label>
						<div class="col-lg-5 position-relative">
							<x-input type="text" name="product_" id="search_product" placeholder="{!! ucfirst(trans('textos.producto')) !!}" maxlength="150"/>
						</div>

						<input type="hidden" name="type" id="productType" value="p">	{{-- Filtra por el tipo de producto a buscar: producto o servicio --}}
						<input type="hidden" name="type" id="productOnSale" value="1">	{{-- Filtra si el producto se comercializa o no --}}

						<input type="hidden" name="product_id" id="productId">

						{{-- Código --}}
						<label for="ref" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.referencia')) !!}</label>
						<div class="col-lg-3">
							<x-input type="text" name="ref" id="productRef" maxlength="150" readonly/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Cantidad --}}
						<label for="qty" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cantidad')) !!}</label>
						<div class="col-lg-1">
							<x-input type="text" name="qty" class="text-right set-decimals" id="productQty" value="1" autocomplete="off" maxlength="9"/>
						</div>

						{{-- Precio --}}
						<label for="price" class="col-lg-1 col-form-label">{!! ucfirst(trans('textos.precio')) !!}</label>
						<div class="col-lg-2 position-relative">
							@php
							$cur = $currency? $currency->symbol:'';
							@endphp
							<x-input type="text" name="price" class="text-right set-decimals" id="productPrice" value="" autocomplete="off" maxlength="8" group="right" groupvalue="{{ $cur }}"/>
						</div>

						{{-- Descuento --}}
						<label for="discount" class="col-lg-1 col-form-label">{!! ucfirst(trans('textos.descuento')) !!}</label>
						<div class="col-lg-2 position-relative">
							<x-input type="text" name="discount[]" class="text-right set-decimals" id="productDiscount" value="" autocomplete="off" maxlength="6" group="right" groupvalue="%"/>
						</div>

						{{-- IVA --}}
						<label for="iva" class="col-lg-1 col-form-label">IVA</label>
						<div class="col-lg-2 position-relative">
							<x-input type="text" name="iva" class="text-right set-decimals" id="productIva" value="" autocomplete="off" maxlength="6" group="right" groupvalue="%"/>	
						</div>
					</div>

					{{-- Observaciones --}}
					<div class="form-group row">
						<label for="observations" class="col-lg-2 col-form-label">{{ ucfirst(trans('textos.observaciones')) }}</label>
						<div class="col-lg-10">
							<x-input-textarea name="observations"/>
						</div>
					</div>

					<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

{{-- Items --}}
@if($items->count())
	<div class="row">
		<div class="table-responsive">
			<table class="table table-hover dTable" id="tblCashItems">			
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>{{ ucfirst(trans('textos.producto')) }}</th>
						<th class="text-center">{{ ucfirst(trans('textos.cantidad')) }}</th>
						<th class="text-center">{{ ucfirst(trans('textos.precio_unidad')) }}</th>
						<th class="text-center">IVA</th>
						<th class="text-center">{{ ucfirst(trans('textos.descuentos')) }}</th>
						<th class="text-center">{{ ucfirst(trans('textos.importe')) }}</th>
						<th>{{ ucfirst(trans('textos.estado')) }}</th>
						<th class="text-center">{{ ucfirst(trans('textos.acciones')) }}</th>
					</tr>
				</thead>

				<tbody>
					@php
					$k = 1;
					@endphp

					@foreach($items as $i)
						@php
						$item_amount = App\Helpers\helpers::itemAmount($i, true);
						@endphp

						<tr>
							{{-- Orden --}}
							<td class="text-right">{{ $k }}</td>
							{{-- Producto --}}
							<td>{{ $i->concept }}</td>
							{{-- Cantidad --}}
							<td class="text-right">{{ $i->qty }}</td>
							{{-- Precio unitario --}}
							<td class="text-right">{{ $i->price }}{{ $currency->symbol }}</td>
							{{-- IVA --}}
							<td class="text-right">{{ $i->iva }}%</td>
							{{-- Descuentos --}}
							<td class="text-right">
								@if($i->discount)
									{{ $i->discount }}%
								@endif
							</td>
							{{-- Importe --}}
							<td class="text-right">
								{{ $item_amount['amount'] }}{{ $currency->symbol }}
							</td>
							{{-- Estado --}}
							<td></td>
							{{-- Acciones --}}
							<td class="text-right">
								{{-- Editar --}}
								<a href="#" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>

								{{-- Eliminar --}}
								@can('cash.edit')
									<form method="POST" action="{{ route('items.destroy', $i->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
									    <input name="_method" type="hidden" value="DELETE">
									    {{ csrf_field() }}
									    <input type="hidden" name="environment" value="cash">
									    <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.item_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
									</form>
								@endif
							</td>
						</tr>

						@php
						$k++;
						@endphp
					@endforeach
				</tbody>	
			</table>
		</div>
	</div>
@endif

{{-- Modals: --}}
@include('admin.companies.partials.modal-add-company', ['business_relationship' => 'customers', 'return' => 'cash', 'return_id' => $cash->id])

@endsection
<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('orders.list') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}
				
					<div class="form-group row">
						<div class="col-md-10 offset-md-2">
							<p class="text-warning">{!! trans('textos.selec_cliente_proveedor') !!}</p>
						</div>

						{{-- Selectores de cliente y proveedor --}}
						@include('admin.partials.selector-customer', ['all' => true, 'selected' => $customer_id])

						@include('admin.partials.selector-provider', ['all' => true, 'selected' => $provider_id])
					</div>

					<div class="form-group row">
						{{-- Año --}}
						@php
						$year = false;
						$selected_year = $year? $year:date('Y');
						@endphp
						@include('admin.partials.selector-year', ['selected_year' => $selected_year])

						{{-- Pedido --}}
						<label for="order_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.pedido_num')) !!} </label>
						<div class="col-lg-2">
							<input class="form-control" type="text" name="order_id" maxlength="25">
						</div>

						{{-- Propietario --}}
						@if(isset($owners))
							<label for="owner" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.propietario')) !!} </label>
							<div class="col-lg-2">
								@php
								$owners_['0'] = trans('textos.todos');
								foreach($owners as $own){
									$owners_[$own->id] = $own->name.' '.$own->surname;
								}
								@endphp

								<x-select name="owner" class="select2" :options="$owners_" value="{{ $owner }}"/>
							</div>
						@endif
					</div>

					<x-btn-submit name="submit_filter" label="{{ trans('textos.buscar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>
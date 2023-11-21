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
						$selected_year = $year? $year:date('Y');
						@endphp
						@include('admin.partials.selector-year', ['selected_year' => $selected_year])

						{{-- Desde --}}
						<label for="from" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.desde')) !!}</label>
						<div class="col-lg-2">
							@php
							$from_ = $from != ''? \Carbon\Carbon::parse($from)->format($languages[session()->get('locale')][4]):'';
							@endphp

							<x-date-picker name="from" value="{{ $from_ }}" :format="$languages[config('app.locale')][4]"/>	
						</div>

						{{-- Hasta --}}
						<label for="to" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.hasta')) !!}</label>
						<div class="col-lg-2">
							@php
							$to_ = $to != ''? \Carbon\Carbon::parse($to)->format($languages[session()->get('locale')][4]):'';
							@endphp

							<x-date-picker name="to" value="{{ $to_ }}" :format="$languages[config('app.locale')][4]"/>	
						</div>
					</div>

					<div class="form-group row">
						{{-- Pedido --}}
						<label for="order_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.pedido_num')) !!} </label>
						<div class="col-lg-2">
							<input class="form-control" type="text" name="order_id" maxlength="25">
						</div>

						{{-- Orden de compra --}}
						<label for="order_buy" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.orden_compra')) !!} </label>
						<div class="col-lg-2">
							<input class="form-control" type="text" name="order_buy" maxlength="25">
						</div>

						{{-- Estado --}}
						<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.estado')) !!} </label>
						<div class="col-lg-2">
							@php
							//Rehacemos el array de estados para incluir en primer lugar la opción de Todos. Descartamos el uso de la función array_unshift pues resetea los índices del resto de opciones, lo cual no interesa.
							$order_status_[0] = trans('textos.todos');
							foreach($order_status as $k => $os){
								$order_status_[$k] = $os;
							}
							@endphp

							<x-select name="status" class="select2" :options="$order_status_" label="estado_selec" value="{{ $status }}"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Propietario --}}
						@if(isset($owners))
							<label for="owner" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.manager')) !!} </label>
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

						{{-- Grupos --}}
						@if(count($groups))
							<label for="groups" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.grupos')) !!} </label>
							<div class="col-lg-4">
								@php
								$groups_selected = $groups_? explode('-', $groups_):[];
								@endphp
								<x-select name="groups[]" class="select2" :options="$groups" multiple :values="$groups_selected"/>	
							</div>
						@endif
					</div>

					<x-btn-submit name="submit_filter" label="{{ trans('textos.buscar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>
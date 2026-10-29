<div class="row">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('effects.list') }}" accept-charset="utf-8"
				enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						<div class="col-md-10 offset-md-2">
							<p class="text-warning">{!! trans('textos.efecto_selec_cliente_proveedor') !!}</p>
						</div>
					</div>

					<div class="form-group row">
						{{-- Clientes --}}
						<label for="customer" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.cliente')) !!}</label>
			            <div class="col-md-4 position-relative">
			                <x-input type="text" name="customer" id="search_customer" placeholder="{!! ucfirst(trans('textos.cliente')) !!}" maxlength="150"/>
			            </div>

			            <input type="hidden" name="customer_id" id="customerId">

			            {{-- Proveedores --}}
			            <label for="provider" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.proveedor')) !!}</label>
			            <div class="col-md-4 position-relative">
			                <x-input type="text" name="provider" id="search_provider" placeholder="{!! ucfirst(trans('textos.proveedor')) !!}" maxlength="150"/>
			            </div>

			            <input type="hidden" name="provider_id" id="providerId">
			        </div>

			        {{-- 02/01/2024: se eliminan los filtros por año, nº efecto y propietario. Sólo filtro por cliente o proveedor --}}
					{{-- <div class="form-group row"> --}}
						{{-- Año --}}
						{{-- @php
						$year = false;
						$selected_year = $year? $year:date('Y');
						@endphp
						@include('admin.partials.selector-year', ['selected_year' => $selected_year]) --}}

						{{-- Efecto --}}
						{{-- <label for="effect" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.efecto_num')) !!} </label>
						<div class="col-lg-2">
							<input class="form-control" type="text" name="effect" maxlength="25">
						</div> --}}

						{{-- Propietario --}}
						{{-- @if(isset($owners))
							<label for="owner"
							 class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.propietario')) !!} </label>
							<div class="col-lg-2">
								@php
								$owners_[0] = trans('textos.todos');
								foreach($owners as $own){
									$owners_[$own->id] = $own->name.' '.$own->surname;
								}
								@endphp

								<x-select name="owner" class="select2" :options="$owners_" value="{{ $owner }}"/>
							</div>
						@endif
					</div> --}}

					<x-btn-submit name="submit_filter" label="{{ trans('textos.buscar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

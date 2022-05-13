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

		{{-- Nueva agrupación --}}
		@can('orders.create')
			<a href="{{ route('order-groups.create') }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.agrupacion_nueva')) !!}
    		</a>
    	@endcan

    	{{-- Agregar pedidos: --}}
    	@can('orders.edit')
			@if($group->status == config('constants.ORDER_GROUPS_OPENED_'))
				<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-add-orders">
					<i class="fa fa-plus"></i>
					{!! trans('textos.pedidos_agregar') !!}
				</a>
			@endif
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-3">
					{!! ucfirst(trans('textos.agrupacion')) !!} 
					<u>
						@if($group->name)
							{{ $group->name }}
						@else
							{{ $group->id }}
						@endif
					</u>
				</h3>

				{{-- Info --}}
				<div class="mb-3">
					{{-- Cliente --}}
					@include('admin.budget_order_shared_fields.partials.data-customer')

					{{-- Proveedor --}}
					@include('admin.budget_order_shared_fields.partials.data-provider')

					{{-- Fecha alta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.fecha_alta')) !!}: 
						<b>
							{{ $group->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Fecha última actualización --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.ultima_edicion')) !!}: 
						<b>
							{{ $group->updated_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>
				</div>

				{{-- Tabs: --}}
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item @if(!$tab) active @endif">
			    		<a class="nav-link @if(!$tab) active @endif" href="#tab-info" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.agrupacion')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'orders') active @endif">
			  			<a class="nav-link @if($tab == 'orders') active @endif" href="#tab-group-orders" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.pedidos')) !!}</a>	
			  		</li>
			  	</ul>

			  	<div class="tab-content pt-4">
					{{-- Agrupación: --}}
					<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-info">
						@include('admin.order_groups.partials.group-info')	
					</div>

					{{-- Pedidos de la agrupación: --}}
					<div class="tab-pane fade @if($tab == 'orders') show active @endif" role="tabpanel" id="tab-group-orders">
						@include('admin.order_groups.partials.group-orders')	
					</div>	
				</div>
			</div>
		</div>
	</div>
</div>

{{-- Modals: --}}
{{-- Add orders: --}}
@if($group->status == config('constants.ORDER_GROUPS_OPENED_'))
	<div class="modal fade modal-add-orders" id="modal-add-orders" tabindex="-1" role="dialog" aria-labelledby="newOrder" aria-hidden="true">
	    <div class="modal-dialog modal-xl" role="document">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h4 class="modal-title" id="newOrder">{!! ucfirst(trans('textos.pedidos_agregar')) !!}</h4>
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    <span aria-hidden="true">&times;</span>
	                </button>
	            </div>

	            <div class="modal-body">
	            	@if(count($orders_off))
		                <form method="post" autocomplete="off" action="{{ route('order-groups-orders.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
		                    {{ csrf_field() }}
		                    <input type="hidden" name="group_id" value="{{ $group->id }}">

		                    <table class="modal-table" id="tblOrdersOff">
			            		<thead>
			            			<tr>
			            				<th class="text-center">#</th>
			            				<th class="text-center">{!! ucfirst(trans('textos.numero_antiguo')) !!}</th>
			            				<th>{!! ucfirst(trans('textos.pedido_titulo')) !!}</th>
			            				<th class="text-center" style="width: 125px;">{!! ucfirst(trans('textos.fecha_entrega')) !!}</th>
			            				<th class="text-center">{!! ucfirst(trans('textos.facturacion')) !!}</th>
			            				<th class="text-center">{!! ucfirst(trans('textos.seleccionar')) !!}</th>
			            			</tr>
			            		</thead>

			            		<tbody>
			            			@foreach($orders_off as $off)
			            				<tr>
			            					<td class="text-right">
			            						@php
												$num = $off->order_num? $off->order_num:$off->order_id;
												@endphp
												<a target="_blank" href="{{ route('orders.edit', $off->order_id) }}">{{ $num }}</a>
			            					</td>
			            					<td class="text-right">{{ $off->old_order }}</td>
			            					<td>{{ $off->name }}</td>
			            					<td class="text-right">
			            						@php
			            						$fechaEntrega = $off->delivery_date && $off->delivery_date != config('constants.DATE_NULL_')? Carbon\Carbon::parse($off->delivery_date)->format(config('locale.languages.'.config('app.locale'))[4]):'';
			            						@endphp

			            						{{ $fechaEntrega }}
			            					</td>
			            					<td>
			            						@php
			            						$month = $off->month? App\Helpers\Helpers::yearMonths($off->month):'';
			            						@endphp
			            						{{ $off->year }} {{ $month }} @if($off->percent) <small>({{ $off->percent }}%)</small> @endif
			            					</td>
			            					<td class="text-center">
			            						<label class="custom-chk-rdb">
                            						<input type="checkbox" name="previsions[]" value="{{ $off->id }}"><span class="chk chksmall" style="margin-top: 2px;"></span>
                        						</label>
			            					</td>
			            				</tr>
			            			@endforeach	
			            		</tbody>
			            	</table>

			            	<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
		                </form>   

		            @else
		            	<h4 class="text-center text-warning mb-4 mt-4">{{ trans('textos.no_pedidos_agrupables') }}</h4>
		            @endif 
	            </div>
	        </div>
	    </div>
	</div>
@endif

@endsection
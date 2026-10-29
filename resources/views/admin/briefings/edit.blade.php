@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-sm-12">
		{{-- Volver a briefings --}}
		<a href="{{ route('briefings.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_briefings') !!}
		</a>

		{{-- Nuevo briefing --}}
		@can('briefings.create')
    		<a href="{{ route('briefings.create') }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.briefing_nuevo')) !!}
    		</a>
        @endcan

        {{-- Presupuesto / pedido --}}
        @if($order)
        	@if(!$order->is_order)
        		@can('budgets.edit')
	        		<a href="{{ route('budgets.edit', $order->id) }}" class="btn btn-info">
	    				<i class="fa fa-edit"></i>
	    				{!! ucfirst(trans('textos.presupuesto_editar')) !!}
	    			</a>
	    		@endcan	
        	@else
        		@can('orders.edit')
	        		<a href="{{ route('orders.edit', $order->id) }}" class="btn btn-info">
	    				<i class="fa fa-edit"></i>
	    				{!! ucfirst(trans('textos.pedido_editar')) !!}
	    			</a>
	    		@endcan	
        	@endif
        @endif

        @can('briefings.destroy')
			{{-- Eliminar --}}
			<form method="POST" action="{{ route('briefings.destroy', $briefing) }}" class="frm-inline" role="form" accept-charset="UTF-8">
			    <input name="_method" type="hidden" value="DELETE">
			    {{ csrf_field() }}
			    <button type="submit" class="btn btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.briefing_eliminar_confirm')) }}"><i class="fa fa-trash"></i></button>
			</form>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-3">
					{!! ucfirst(trans('textos.briefing')) !!}
					<u>{{ $briefing->name }}</u>
				</h3>

				{{-- Info --}}
				<div class="mb-3">
					{{-- Fecha alta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.fecha_alta')) !!}:
						<b>
							{{ $briefing->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Pedido --}}
					@if($briefing->order_id)
						<label class="mr-5">
							{!! ucfirst(trans('textos.cliente')) !!}:
							<b>
								{{ $briefing->order->order_num }}
							</b>
						</label>	
					@endif

					{{-- Cliente --}}
					@if($briefing->customer_id)
						<label class="mr-5">
							{!! ucfirst(trans('textos.cliente')) !!}:
							<b>
								{{ $briefing->customer->name }}
							</b>
						</label>
					@endif

					{{-- Creado por --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.creado_por')) !!}: 
						<b>
							{{ $briefing->createdBy->name }} {{ $briefing->createdBy->surname }}
						</b>
					</label>

					{{-- Fecha última actualización --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.ultima_edicion')) !!}:
						<b>
							{{ $briefing->updated_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Actualizado por --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.actualizado_por')) !!}: 
						<b>
							{{ $briefing->updatedBy->name }} {{ $briefing->updatedBy->surname }}
						</b>
					</label>

					{{-- Plantilla --}}
					@if($briefing->template_id)
						<label class="mr-5">
							{!! ucfirst(trans('textos.plantilla')) !!}:
							<b>
								{{ $briefing->template->name }}
							</b>
						</label>
					@endif
				</div>

				{{-- Tabs: --}}
				<ul class="nav nav-tabs" role="tablist">	
					<li class="nav-item @if(!$tab) active @endif">
			    		<a class="nav-link @if(!$tab) active @endif" href="#tab-briefing-info" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.info_general')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'items') active @endif">
			    		<a class="nav-link @if($tab == 'items') active @endif" href="#tab-briefing-items" role="tab" data-toggle="tab">Items</a>
			  		</li>
			  	</ul>

			  	{{-- Panels --}}
			  	<div class="tab-content pt-5">
					{{-- Info --}}
					<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-briefing-info">
						@include('admin.briefings.partials.briefing-info')	
					</div>

					{{-- Items --}}
					<div class="tab-pane fade @if($tab == 'items') show active @endif" role="tabpanel" id="tab-briefing-items">
						@include('admin.briefings.partials.briefing-items')	
					</div>
				</div>	
			</div>
		</div>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Formulario edición item.
	$(document).on('submit', '.bil-frm', function(e){
		e.preventDefault();
        var data = $(this).serialize();

        $.ajax({
            url: "/admin/briefing-items/update",
            type: "POST",
            data: data,
            cache: false,
            datatype: "JSON",
            success: function (response) {
                divMsg(response);
            }
        });
	});

});
</script>
@endpush

@endsection
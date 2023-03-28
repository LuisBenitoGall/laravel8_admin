@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Volver a SATs --}}
		<a href="{{ route('sat.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_sat') !!}
		</a>	

		{{-- Nuevo servicio --}}
		@can('sat.create')
			<a href="{{ route('sat.create') }}" class="btn btn-info">
				<i class="fa fa-plus"></i>
				{!! ucfirst(trans('textos.sat_nuevo')) !!}
			</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-3">
					{{ ucfirst(trans('textos.servicio')) }}
					<u>
						@if($sat->name) {{ $sat->name }} @else {{ $sat->id }} @endif
					</u>
				</h3>

				{{-- Info --}}
				<div class="mb-3">
					{{-- Cliente --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.cliente')) !!}: 
						<b>
							{{ ucfirst($customer->name) }}
						</b>
					</label>

					{{-- Proveedor --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.proveedor')) !!}: 
						<b>
							@if($provider)
								{{ ucfirst($provider->name) }}
							@else
								<span class="text-warning">{{ trans('textos.sin_definir') }}</span>
							@endif
						</b>
					</label>

					{{-- Fecha alta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.fecha_alta')) !!}:
						<b>
							{{ $sat->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Fecha última actualización --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.ultima_edicion')) !!}:
						<b>
							@if($sat->updated_at) {{ $sat->updated_at->format($languages[session()->get('locale')][4].' H:i:s') }} @endif
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

				{{-- Tabs: --}}
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item @if(!$tab) active @endif">
			    		<a class="nav-link @if(!$tab) active @endif" href="#tab-info" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.info_general')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'sale') active @endif">
			  			<a class="nav-link @if($tab == 'sale') active @endif" href="#tab-sale" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.venta')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'purchase') active @endif">
			  			<a class="nav-link @if($tab == 'purchase') active @endif" href="#tab-purchase" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.compra')) !!}</a>
			  		</li>
			  	</ul>

			  	<div class="tab-content pt-4">
					{{-- Cabecera --}}
					<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-info">
						@include('admin.sats.partials.sat-info')
					</div>

					{{-- Venta --}}
					<div class="tab-pane fade @if($tab == 'sale') show active @endif" role="tabpanel" id="tab-sale">
						@include('admin.sats.partials.sat-sale')
					</div>

					{{-- Purchase --}}
					<div class="tab-pane fade @if($tab == 'purchase') show active @endif" role="tabpanel" id="tab-purchase">
						@include('admin.sats.partials.sat-purchase')
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

{{-- Modals: --}}
{{-- Add purchase --}}
@include('admin.sats.partials.modal-add-item')

@push('scripts')
<script>
$(function(){
	//Edición información tracking:
	$('.btnEditInformation').on('click', function(){
		var padre = $(this).closest('.timeline-item');
		var info = padre.find('.content p').text();
		padre.find('.content').append('<textarea class="form-control">'+info+'</textarea>');
	});

});
</script>
@endpush

@endsection
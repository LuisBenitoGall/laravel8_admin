@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="amortizationId" value="{{ $amortization->id }}">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Volver a amortizaciones --}}
		<a href="{{ route('amortizations.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_amortizaciones') !!}
		</a>

		@can('amortizations.create')
			<a class="btn btn-info" href="{{ route('amortizations.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.amortizacion_nueva')) !!}</a>
		@endcan

		{{-- Nueva línea --}}
		@can('amortizations.edit')
			<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-new-line">
				<i class="fa fa-plus"></i> {!! ucfirst(trans('textos.linea_nueva')) !!}
			</a>
		@endcan
	</div>
</div>
		
<div class="row mb-3">
	<div class="col-12">
		<div class="card card-form">
			<div class="card-body">
				<h3 class="mb-3">{!! trans('textos.amortizacion') !!} <u>{{ $amortization->description }}</u></h3>

				{{-- Info --}}
				<div class="mb-3">
					{{-- Fecha alta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.fecha_alta')) !!}: 
						<b>
							{{ $amortization->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Fecha última actualización --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.ultima_edicion')) !!}: 
						<b>
							{{ $amortization->updated_at->format($languages[session()->get('locale')][4].' H:i:s') }}
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
					<a class="nav-link @if(!$tab) active @endif" href="#tab-info" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.datos_generales')) !!}</a>
					</li>

					<li class="nav-item @if($tab == 'items') active @endif">
						<a class="nav-link @if($tab == 'items') active @endif" href="#tab-items" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.amortizacion_lineas')) !!}</a>	
					</li>
				</ul>

				<div class="tab-content pt-4">
					{{-- Cabecera --}}
					<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-info">
						@include('admin.amortizations.partials.amortization-info')	
					</div>

					{{-- Items --}}
					<div class="tab-pane fade @if($tab == 'items') show active @endif" role="tabpanel" id="tab-items">
						@include('admin.amortizations.partials.amortization-items')	
					</div>
				</div>
			</div>
		</div>	
	</div>
</div>

{{-- Modals: --}}
{{-- Add line --}}
@include('admin.amortizations.partials.modal-add-line')

{{-- Edit line --}}
@include('admin.amortizations.partials.modal-edit-line')

@push('scripts')
<script>
$(function(){
	//Edición línea de amortización:
	$(document).on('click', '.btnEditLine', function(){
		var id = $(this).data('id');
		var amount = $(this).data('amount');
		var percent = $(this).data('percent');
		
		$('#lineId').val(id);
		$('#lineAmount').val(amount);
		$('#linePercent').val(percent);
	});
});
</script>
@endpush

@endsection
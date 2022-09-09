<form method="post" autocomplete="off" action="{{ route('orders.update-template', $order) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	@method('PUT')
    {{ csrf_field() }}
    <input type="hidden" name="order_id" value="{{ $order->id }}">

    <div class="row">
    	<div class="col-md-10 offset-md-2">
    		<p class="text-warning">{!! trans('textos.presupuesto_plantilla_texto') !!}</p>
    	</div>
    </div>

    <div class="form-group row">
    	{{-- Selector de template --}}
		<label for="template_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.plantilla')) !!} *</label>
		<div class="col-lg-4">
			@php
			$templates_ = [];
			@endphp

			@if($templates)
				@foreach($templates as $tmp)
					@php
					$templates_[$tmp->id] = $tmp->name;
					@endphp	
				@endforeach
			@endif

			<x-select name="template_id" class="select2" :options="$templates_" label="plantilla_selec" value="{{ $order->order_template_id }}" required/>
		</div>

		{{-- Reset valores originales de la plantilla --}}
		@if($order->template_id && $templates->count() > 0)
			<div class="col-lg-2">
				<a href="{{ route('orders.remove-template', $order) }}" class="btn btn-warning btn-confirm" rel="tooltip" title="{!! ucfirst(trans('textos.plantilla_reestablecer')) !!}" data-msg="{!! ucfirst(trans('textos.plantilla_reestablecer_confirm')) !!}">
					<i class="fa fa-redo-alt"></i>
				</a>
			</div>
		@endif
	</div>
	<hr>

	@php
	$template = $order->order_template? unserialize($order->order_template):false;
	@endphp

	{{-- Condiciones --}}
	<div class="form-group row">
		<label for="conditions" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.condiciones')) !!}</label>
		<div class="col-lg-10">
			@php
			$conditions = $template? $template['conditions']:'';
			@endphp

			<x-input-textarea name="conditions" class="editor" value="{{ $conditions }}"/>
		</div>
	</div>

	{{-- Método de pago --}}
	<div class="form-group row">
		<label for="payment_method" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.pago_metodo')) !!}</label>
		<div class="col-lg-10">
			@php
			$payment_method = $template? $template['payment_method']:'';
			@endphp

			<x-input-textarea name="payment_method" class="editor" value="{{ $payment_method }}"/>
		</div>
	</div>

	{{-- Observaciones --}}
	<div class="form-group row">
		<label for="observations" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.observaciones')) !!}</label>
		<div class="col-lg-10">
			@php
			$observations = $template? $template['observations']:'';
			@endphp

			<x-input-textarea name="observations" class="editor" value="{{ $observations }}"/>
		</div>
	</div>

	{{-- Pie de página --}}
	<div class="form-group row">
		<label for="footer_legend" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.pie_pagina')) !!}</label>
		<div class="col-lg-10">
			@php
			$footer_legend = $template? $template['footer_legend']:'';
			@endphp

			<x-input-textarea name="footer_legend" class="editor" value="{{ $footer_legend }}"/>
		</div>
	</div>

	<x-btn-submit name="submit_update_template" label="{{ trans('textos.guardar') }}"/>
</form>
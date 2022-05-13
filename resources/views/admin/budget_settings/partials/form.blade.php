<div class="row">
	<div class="col-md-10 offset-md-2">
		<p class="text-warning">{!! trans('textos.presupuesto_plantilla_texto') !!}</p>
	</div>
</div>

<div class="form-group row">
	{{-- Nombre plantilla --}}
	<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.plantilla')) !!} *</label>
	<div class="col-lg-6">
		@php
		$name = $action == 'update'? $template->name:'';
		@endphp

		<x-input type="text" name="name" placeholder="{!! ucfirst(trans('textos.plantilla')) !!}" autocomplete="off" maxlength="150" required value="{{ $name }}"/>
	</div>	

	{{-- Estado --}}
	<label for="status" class="col-lg-1 col-form-label">{!! ucfirst(trans('textos.activo')) !!}</label>
	<div class="col-lg-1">
		<label class="custom-chk-rdb" rel="tooltip">
            <input type="checkbox" name="status" @if( (isset($template) && $template->status == 1) || $action == 'create') checked @endif>
            <span class="chk" style="top: 5px;"></span>
        </label>
	</div>
</div>

<div class="form-group row">
	{{-- Condiciones --}}
	<label for="conditions" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.condiciones')) !!}</label>
	<div class="col-lg-10">
		@php
		$conditions = $action == 'update'? $template->conditions:'';
		@endphp
		<x-input-textarea name="conditions" class="editor" value="{{ $conditions }}"/>
	</div>
</div>

<div class="form-group row">
	{{-- Métodos de pago --}}
	<label for="payment_method" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.pago_metodo')) !!}</label>
	<div class="col-lg-10">
		@php
		$method = $action == 'update'? $template->payment_method:'';
		@endphp
		<x-input-textarea name="payment_method" class="editor" value="{{ $method }}"/>
	</div>
</div>

<div class="form-group row">
	{{-- Observaciones --}}
	<label for="observations" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.observaciones')) !!}</label>
	<div class="col-lg-10">
		@php
		$observations = $action == 'update'? $template->observations:'';
		@endphp
		<x-input-textarea name="observations" class="editor" value="{{ $observations }}"/>
	</div>
</div>

<div class="form-group row">
	{{-- Pie de página --}}
	<label for="footer_legend" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.pie_pagina')) !!}</label>
	<div class="col-lg-10">
		@php
		$footer_legend = $action == 'update'? $template->footer_legend:'';
		@endphp
		<x-input-textarea name="footer_legend" class="editor" value="{{ $footer_legend }}"/>
	</div>
</div>
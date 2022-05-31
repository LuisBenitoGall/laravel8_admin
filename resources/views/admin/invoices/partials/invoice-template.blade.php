<form method="post" autocomplete="off" action="{{ route('invoices.update-template', $invoice) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	@method('PUT')
    {{ csrf_field() }}

    <div class="row">
    	<div class="col-md-10 offset-md-2">
    		<p class="text-warning">{!! trans('textos.pedido_plantilla_texto') !!}</p>
    	</div>
    </div>

    <div class="form-group row">
    	{{-- Selector plantilla --}}
		<label for="template_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.plantilla')) !!} *</label>
		<div class="col-lg-4">
			@php
			$templates_ = [];
			$featured = '';
			foreach($templates as $tmp){
				$templates_[$tmp->id] = $tmp->name;	
				if($tmp->featured == 1) $featured = $tmp->id;
			}

			$featured_ = $invoice->template_id? $invoice->template_id:$featured;
			@endphp

			<x-select name="template_id" class="select2" label="plantilla_selec" :options="$templates_" value="{{ $featured_ }}"/>
		</div>

		@if(count($templates) == 0)
			<div class="col-lg-2">
				@can('invoices.edit')
					<a target="_blank" class="btn btn-info" href="{{ route('invoice-settings.create') }}">	<i class="fa fa-plus"></i> {!! ucfirst(trans('textos.plantilla_nueva')) !!}
					</a>
				@endcan
			</div>
		@endif
	</div>
	<hr>

	@php
	$template = $invoice->template? unserialize($invoice->template):false;
	@endphp

	<div class="form-group row">
		{{-- Condiciones --}}
		<label for="conditions" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.condiciones')) !!}</label>
		<div class="col-lg-10">
			@php
			$conditions = $template? $template['conditions']:'';
			@endphp
			<x-input-textarea name="conditions" class="editor" value="{{ $conditions }}"/>
		</div>
	</div>

	<div class="form-group row">
		{{-- Métodos de pago --}}
		<label for="payment_method" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.pago_metodo')) !!}</label>
		<div class="col-lg-10">
			@php
			$payment_method = $template? $template['payment_method']:'';
			@endphp
			<x-input-textarea name="payment_method" class="editor" value="{{ $payment_method }}"/>
		</div>
	</div>

	<div class="form-group row">
		{{-- Observaciones --}}
		<label for="observations" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.observaciones')) !!}</label>
		<div class="col-lg-10">
			@php
			$observations = $template? $template['observations']:'';
			@endphp
			<x-input-textarea name="observations" class="editor" value="{{ $observations }}"/>
		</div>
	</div>

	<x-btn-submit name="submit_update_template" label="{{ trans('textos.guardar') }}"/>
</form>
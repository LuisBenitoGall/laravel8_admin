@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Cierre mensual ventas --}}
	<div class="col-md-4">
		<div class="widget-head-color-box navy-bg p-lg text-center">
            <div class="m-b-md">
            	<h2 class="font-bold no-margins">{{ trans('textos.cierre_mensual_ventas') }}</h2>
                <small>{{ trans('textos.cierre_mensual_ventas_texto') }}</small>
            </div>

            <div>
            	@php
            	$label = $options && $options->monthly_closing_sales? 'desactivar':'activar';
            	@endphp

            	<form method="post" autocomplete="off" action="{{ route('accounting-options.store') }}" accept-charset="utf-8" role="form">
					{{ csrf_field() }}
					<input type="hidden" name="field" value="monthly_closing_sales">
            			
					<input type="submit" name="submit_save" class="btn btn-warning" value="{{ trans('textos.'.$label) }}">
		        </form>

		        @if($options && $options->monthly_closing_sales)
                	<span>{{ trans('textos.activo_desde') }} {{ \Carbon\Carbon::parse($options->monthly_closing_sales)->format($languages[session()->get('locale')][4]) }}</span>
                @endif
            </div>
        </div>
	</div>

	{{-- Cierre mensual compras --}}
	<div class="col-md-4">
		<div class="widget-head-color-box navy-bg p-lg text-center">
            <div class="m-b-md">
            	<h2 class="font-bold no-margins">{{ trans('textos.cierre_mensual_compras') }}</h2>
                <small>{{ trans('textos.cierre_mensual_compras_texto') }}</small>
            </div>

            <div>
            	@php
            	$label = $options && $options->monthly_closing_purchases? 'desactivar':'activar';
            	@endphp

            	<form method="post" autocomplete="off" action="{{ route('accounting-options.store') }}" accept-charset="utf-8" role="form">
					{{ csrf_field() }}
					<input type="hidden" name="field" value="monthly_closing_purchases">
            			
					<input type="submit" name="submit_save" class="btn btn-warning" value="{{ trans('textos.'.$label) }}">
		        </form>

		        @if($options && $options->monthly_closing_purchases)
                	<span>{{ trans('textos.activo_desde') }} {{ \Carbon\Carbon::parse($options->monthly_closing_purchases)->format($languages[session()->get('locale')][4]) }}</span>
                @endif
            </div>
        </div>
	</div>
</div>

@endsection
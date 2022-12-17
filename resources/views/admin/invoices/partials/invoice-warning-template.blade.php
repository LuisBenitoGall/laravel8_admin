@if($accounting_options)
	@php
	$mes = date('m');
    $any = date('Y');
    @endphp

	{{-- Cierre mensual ventas --}}
	@if($accounting_options->monthly_closing_sales)
		@php
		$time = strtotime($accounting_options->monthly_closing_sales);
        $month = date("m",$time);
        $year = date("Y",$time);
        @endphp

        @if($month == $mes && $year == $any)
			<div class="alert alert-warning" role="alert">
				{!! trans('textos.cierre_mensual_ventas_aviso') !!}
			</div>
		@endif
	@endif

	{{-- Cierre mensual compras --}}
	@if($accounting_options->monthly_closing_purchases)
		@php
		$time = strtotime($accounting_options->monthly_closing_purchases);
        $month = date("m",$time);
        $year = date("Y",$time);
        @endphp

        @if($month == $mes && $year == $any)
			<div class="alert alert-warning" role="alert">
				{!! trans('textos.cierre_mensual_compras_aviso') !!}
			</div>
		@endif
	@endif
@endif



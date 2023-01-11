@extends('admin.partials.layout')

@section('content')

@php
$first_year = 2021;
@endphp

<div class="row mb-3">
	{{-- Cierre mensual ventas --}}
	<div class="col-md-6">
		<div class="row justify-content-center">
			<div class="col-md-11">
		       	<h2 class="text-center">{{ trans('textos.cierre_mensual_ventas') }}</h2>
		        <p class="text-center">{{ trans('textos.cierre_mensual_ventas_texto') }}</p>

		        @for($i = date('Y'); $i >= $first_year; $i--)
		        	<div class="row group mb-3 pb-3 pt-2" data-year="{{ $i }}">
		        		<div class="col-12 text-center">
		        			<h3 class="text-center">{{ $i }}</h3>
		        		</div>

		        		@foreach($months as $idx => $m)
		        			@php
		        			$closed = in_array($i.'-'.$idx, $sales)? 'closed':'' ;
		        			@endphp

		        			<div class="col-sm-4 col-md-3 mb-2 text-center">
		        				<div class="cell closeAccounting {{ $closed }}" data-month="{{ $idx }}" data-type="s">
		        					<b>{{ ucfirst($m) }}</b>
		        				</div>
		        			</div>
		        		@endforeach

		        	</div>
		        @endfor
        	</div>
		</div>
    </div>

	{{-- Cierre mensual compras --}}
	<div class="col-md-6">
		<div class="row justify-content-center">
			<div class="col-md-11">
				<h2 class="text-center">{{ trans('textos.cierre_mensual_compras') }}</h2>
		        <p class="text-center">{{ trans('textos.cierre_mensual_compras_texto') }}</p>

		        @for($i = date('Y'); $i >= $first_year; $i--)
		        	<div class="row group mb-3 pb-3 pt-2" data-year="{{ $i }}">
		        		<div class="col-12 text-center">
		        			<h3 class="text-center">{{ $i }}</h3>
		        		</div>

		        		@foreach($months as $idx => $m)
		        			@php
		        			$closed = in_array($i.'-'.$idx, $purchases)? 'closed':'' ;
		        			@endphp

		        			<div class="col-sm-4 col-md-3 mb-2 text-center">
		        				<div class="cell closeAccounting {{ $closed }}" data-month="{{ $idx }}" data-type="p">
		        					<b>{{ ucfirst($m) }}</b>
		        				</div>
		        			</div>
		        		@endforeach

		        	</div>
		        @endfor
		    </div>
		</div>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Marcar / desmarcar mes:
	$('.closeAccounting').on('click', function(){
		var elem = $(this);
		var year = elem.closest('.group').data('year');
		var month = elem.data('month');
		var type = elem.data('type');

		$.get("/admin/accounting-options/store/"+year+"/"+month+'/'+type, function(response, state){
			if(response){
				elem.addClass('closed');
			}else{
				elem.removeClass('closed');
			}
		});
	});
});
</script>
@endpush

@endsection
<body>
	<header style="background-color: {{ $bg_color }};">
		<img class="logo" src="{{ asset('img/companies/'.$provider->logo) }}">

		<div class="address">
			{{ $provider->name }} · {{ $provider->nif }}<br>
			@if($provider_workplace && $provider_workplace->address) {{ $provider_workplace->address }} @endif 
			@if($provider_workplace && $provider_workplace->cp)· {{ $provider_workplace->cp}} @endif 
			@if($provider_workplace && $provider_workplace->Town)· {{ $provider_workplace->Town }} @endif<br>
			{{ trans('textos.telefono_abrev') }}: 

			@if($provider_phones)
				@php
				$primero = true;
				@endphp
				@foreach($provider_phones as $phone)
					@if(!$primero) · @endif
					{{ $phone->phone_number }}
					@php
					$primero = false;
					@endphp
				@endforeach
			@endif

			@if($provider_workplace && $provider_workplace->website)
				<br>
				<a target="_blank" href="{{ $provider_workplace->website }}" style="color: #888; line-height: 90%;">{{ $provider_workplace->website }}</a>
			@endif			
		</div>

		<div class="clearfix"></div>
	</header>

	{{-- Condicional de templates --}}
	@if($standard)
		<table class="tblHeader" id="subheader">
			<tbody>
				<tr>
					<th class="tbl-header-left">
						<h1>Factura</h1>	

						{{-- Fecha --}}
						<p style="display: block; font-size: 11px; font-weight: normal; line-height: 110%; margin: 0 0 5px 0; width: 100%;">
							<span class="item-left" style="display: inline-block; vertical-align: top; width: 35%;"><b>{{ strtoupper(trans('textos.fecha')) }}:</b></span> 
							<span class="item-left" style="display: inline-block; vertical-align: top; width: 64%;">{{ $fechaLiteral }}</span>
						</p>

						{{-- Título --}}
						{{-- @if($left_title)
							<p style="display: block; font-size: 11px; font-weight: normal; line-height: 110%; margin: 0 0 5px 0; width: 100%;">
								<span class="item-left" style="display: inline-block; vertical-align: top; width: 35%;"><b>{{ strtoupper(trans('textos.titulo')) }}:</b></span> 
								<span class="item-left" style="display: inline-block; vertical-align: top; width: 64%;">{{ $left_title }}</span>
							</p>	
						@endif --}}

						{{-- Responsable proveedor --}}
						{{-- @if($left_manager)
							<p style="display: block; font-size: 11px; font-weight: normal; line-height: 110%; margin: 0 0 5px 0; width: 100%;">
								<span class="item-left" style="display: inline-block; vertical-align: top; width: 35%;"><b>{{ strtoupper(trans('textos.responsable')) }}:</b></span> 
								<span class="item-left" style="display: inline-block; vertical-align: top; width: 64%;">{{ $left_manager }}</span>
							</p>	
						@endif --}}
					</th>

					<th class="tbl-header-right">
						{{-- <h1>{{ $right_h1 }}</h1> --}}


					</th>
				</tr>
			</tbody>
		</table>
	@endif


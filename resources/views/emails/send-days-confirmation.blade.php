@include('emails.partials.header')

<p>
	<b>{{ $usuario }}</b>
</p>

<p>
	{{ trans('textos.jornadas_confirmadas_texto') }}:
	<br><br>

	@php
	$claves = array_keys($days);
	$i = 0;
	@endphp

	@foreach($days as $day)
		<b>-{{ $claves[$i] }}</b><br>

		@foreach($day as $d)
			@php
			$fecha = isset($d)? Carbon\Carbon::parse($d)->format($languages[session()->get('locale')][4]):false;
			@endphp

			@if($fecha) {{ $fecha }}<br> @endif	
		@endforeach
		@php
		$i++;
		@endphp

		<br><br>
	@endforeach
</p>

@include('emails.partials.footer')
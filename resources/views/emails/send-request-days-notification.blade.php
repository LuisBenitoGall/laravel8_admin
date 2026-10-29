@include('emails.partials.header')

<p style="line-height: 130%;">
	<b>{{ $usuario }}</b> {{ trans('textos.notificar_fechas_solicitadas_email') }}:
	<br><br>

	@php
	$claves = array_keys($days);
	$i = 0;
	@endphp

	@foreach($days as $day)
		<b>-{{ $claves[$i] }}</b><br>

		@foreach($day as $d)
			@php
			$fecha = isset($d)? Carbon\Carbon::parse($d)->format($languages[session()->get('locale')][4]):'';
			@endphp

			{{ $fecha }}<br>	
		@endforeach
		@php
		$i++;
		@endphp

		<br><br>
	@endforeach

	<br>

	<a target="_blank" href="{{ route('work-user-calendar.index', $user_id) }}">{{ trans('textos.calendario_ver') }}</a>
</p>

@include('emails.partials.footer')
@include('emails.partials.header')

<p>
	Solicitud de revisión de horario de <strong>{{ $nombre }} {{ $apellidos }}</strong>
</p>
<br>

<p>
	Hora y fecha: {{ $fecha }} {{ $hora }}, {{ $concepto }}
</p>
<p>
	Comentarios: {{ $comments }}

	<br><br>

	<a target="_blank" href="{{ route('work-user-calendar.index', $user_id) }}">{{ trans('textos.horario_ver') }}</a>
</p>

@include('emails.partials.footer')
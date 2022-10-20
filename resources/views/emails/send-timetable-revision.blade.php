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
</p>

@include('emails.partials.footer')
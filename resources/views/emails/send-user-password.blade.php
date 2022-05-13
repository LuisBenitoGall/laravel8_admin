@include('emails.partials.header')

<p>
	<b>{{ $usuario }}</b>
</p>

<p>
	Aquí tienes tu contraseña para acceder a nuestra plataforma: <a target="_blank" href="{{ env('APP_URL') }}">{{ env('APP_URL') }}</a>
	<br><br>

	<strong>{!! $password !!}</strong>
</p>

@include('emails.partials.footer')
@include('emails.partials.header')

<p>
	<b>{{ $usuario }}</b>
</p>

<p>
	{{ trans('textos.chat_invitacion_email_texto') }}: <a target="_blank" href="{{ env('APP_URL') }}/admin/chat">{{ $chat }}</a>
	<br><br>
</p>

@include('emails.partials.footer')
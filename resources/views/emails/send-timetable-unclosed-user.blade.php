@include('emails.partials.header')

<p>{!! trans('textos.horario_cerrado_automaticamente_aviso') !!} {{ $fecha }}</p>
<br>

<p>{!! trans('textos.hola') !!} {{ $user->name }},</p>
<p>{!! trans('textos.horario_cerrado_automaticamente_texto') !!}</p>

@include('emails.partials.footer')
@include('emails.partials.header')

<p>{!! trans('textos.hola') !!} {{ $user->name }},</p>
<p>{!! trans('textos.horario_texto_push') !!}  {!! $concept !!}</p>

@include('emails.partials.footer')

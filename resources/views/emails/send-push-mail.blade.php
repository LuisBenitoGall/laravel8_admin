@include('emails.partials.header')

<p>{!! trans('textos.hola') !!} {{ $user->name }},</p>
@php
    $concept = strtolower($concept);
@endphp
<p>{!! trans("textos.horario_texto_{$concept}_retorno") !!} </p>

@include('emails.partials.footer')

@include('emails.partials.header')
@php
    $concept = strtolower($concept);
@endphp
<p>{!! trans('textos.hola') !!} {{ $user->name }},</p>
<p>{!! trans("textos.horario_texto_{$concept}") !!}  {!! $concept !!}</p>

@include('emails.partials.footer')

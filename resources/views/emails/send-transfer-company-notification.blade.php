@include('emails.partials.header')

<p>
	<b>{{ $usuario }}</b>
</p>

<p>
	{!! trans('textos.email_traspaso_empresa_1') !!} {{ $company->name }}. 
	<br><br>
	{!! trans('textos.email_traspaso_empresa_2') !!}: <a target="_blank" href="{{ env('APP_URL') }}">{{ env('APP_URL') }}</a> {!! trans('textos.email_traspaso_empresa_3') !!}
	<br><br>

	@if($password)
		{!! trans('textos.email_traspaso_empresa_4') !!} <strong>{!! $password !!}</strong>
	@endif
</p>

@include('emails.partials.footer')
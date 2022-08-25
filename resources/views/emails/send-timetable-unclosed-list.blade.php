@include('emails.partials.header')

<p>
	{{ trans('emails.empleados_lista_horarios_no_cerrados_fecha') }} {{ $fecha }}
</p>
<br>

@foreach($cierres as $row)
	<p>· {{ $row->name }} {{ $row->surname }}, {{ $row->hour }}h.</p>
@endforeach

@include('emails.partials.footer')
@include('emails.partials.header')
<table id="container" style="width: 700px; margin: 0;">
	<tbody>
		<tr>
			<td colspan="2">Solicitud de inscripción de la oferta</td>
		</tr>
		<tr>
			<td>Asunto:</td>
			<td>{{$asunto}}</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2">Datos del interesado</td>
		</tr>
		<tr>
			<td>Nombre(s):</td>
			<td>{{$nombre}}</td>
		</tr>
		<tr>
			<td>Apellido(s):</td>
			<td>{{$apellidos}}</td>
		</tr>
		<tr>
			<td>Correo Electrónico:</td>
			<td><a href="mailto:{{$email}}">{{$email}}</a></td>
		</tr>
		<tr>
			<td>CV:</td>
			<td><a href="{{$fichero}}" target="_blank" rel="noopener noreferrer">Ver fichero</a></td>
		</tr>
		@if ($observaciones)
			<tr>
				<td>Observaciónes:</td>
				<td>{{$observaciones}}</td>
			</tr>
		@endif
		<tr>
			<td colspan="2">Enviado desde la web de https://weareadn.com/</td>
		</tr>
	</tbody>
</table>

@include('emails.partials.footer')
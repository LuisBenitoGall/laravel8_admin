@if($ts->signed)
	<footer>
		<table class="tblFooter">
			<tbody>
				<tr>
					<td style="vertical-align: middle;">
						{{ ucfirst(trans('textos.firma_empleado')) }}
						<br><br>
						{!! ucfirst(trans('textos.fecha')) !!}: {{ date('d/m/Y') }}
					</td>
					<td style="padding-left: 40px; text-align: left;">
						@if(file_exists('img/signatures/'.$user->signature))
							<img src="{{ asset('img/signatures/'.$user->signature) }}" style="max-height: 60px;">
						@endif
					</td>
				</tr>
				<tr>
					<td colspan="2" style="padding-top: 0; text-align: center;">
						{{ trans('textos.informe_validado_text') }} {{ $company->name }} - {{ $company->nif }} - {{ $workplace->address }}, {{ $workplace->cp }} {{ $workplace->town }}
					</td>
				</tr>
			</tbody>
		</table>
	</footer>
@endif

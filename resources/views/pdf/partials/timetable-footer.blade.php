@if($ts->signed)
	<footer>
		<table class="tblFooter">
			<tbody>
				<tr>
					@php
					//28/11/2022: modificando fecha de firma para documentos antiguos y refirmados:
					$change = true;
					
					$meses = [
						'01' => '02',
						'02' => '03',
						'03' => '04',
						'04' => '05',
						'05' => '06',
						'06' => '07',
						'07' => '08',
						'08' => '09',
						'09' => '10',
						'10' => '11',
						'11' => '12',
						'12' => '01'
					];

					$mes = $meses[$ts->month];
					$any = $ts->year;
					if($any == 2021 && $mes == '01'){
						$any == 2022;
					}
					$fecha = $change? '10/'.$mes.'/'.$any:date('d/m/Y');
					@endphp

					<td style="vertical-align: middle;">
						{{ ucfirst(trans('textos.firma_empleado')) }}
						<br><br>
						{!! ucfirst(trans('textos.fecha')) !!}: {{ $fecha }}
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

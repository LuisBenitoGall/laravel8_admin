@php
$provider_workplace = false;
$provider_phones = false;
@endphp

@include('pdf.partials.timetable-head', ['header_title' => ucfirst(trans('textos.horario'))])

@include('pdf.partials.header', [
	'standard' => false,
	'company_settings' => $company_settings, 
	'bg_color' => $bg_color, 
	'left_h1' => false,
	'left_title' => false,
	'left_manager' => false,
	'left_commercial' => false,
	'right_h1' => false,
	'right_manager' => ''
])

@include('pdf.partials.timetable-footer')

<main>

	@if($timetable_settings && $timetable_settings->timetable_doc_message)
		<div style="font-size: 12px;">
			{!! $timetable_settings->timetable_doc_message !!}
		</div>
	@endif

	<table class="tblBody">
		<thead>
			<tr>
				<th>#</th>
				<th>{!! trans('textos.fecha') !!}</th>
				<th>{!! ucfirst(trans('textos.hora')) !!}</th>
				<th>{!! ucfirst(trans('textos.concepto')) !!}</th>
			</tr>
		</thead>

		<tbody>
			@php
			$i = 1;
			$k = 0;
			$horasDia = 0;
			@endphp

			@foreach($horarios as $row)
				@php
				//Guardamos el siguiente índice para posteriores consultas:
				$idx = $k+1;

				$acaba = false;

				//Si el concepto tiene una duración fija, se suman los minutos de esa duración al tiempo trabajado:
				if($row->flag == 1 && $row->fijo > 0){
					$horasDia = App\Helpers\helpers::sumaHoras($horasDia, App\Helpers\Helpers::getTimeFromMinutes($row->fijo));

				//Calculamos el tiempo trabajado sumando los intervalos de tiempo de Entrada y Salida:
				}elseif($row->flag == 1){
					//Tomamos la hora de salida:
					if(isset($horarios[$idx])){
						$acaba = $horarios[$idx]->hour;

						//Si se contemplan ajustes de minutos:
						// if($timetable_settings && $timetable_settings->minute_discount && $timetable_settings->md_from <= $row->date && $timetable_settings->md_to >= $row->date){

						// 	$acaba = strtotime('-'.$timetable_settings->minute_discount.' minute', strtotime($acaba)); // 
						// 	$acaba = date("H:i:s", $acaba); 
						// }

						$tiempo = App\Helpers\helpers::getTimeBetweenHours($row->hour, $acaba);

					}else{
						$tiempo = App\Helpers\helpers::getTimeBetweenHours($row->hour, date('H:i:s'));
					}

					$horasDia = App\Helpers\helpers::sumaHoras($horasDia, $tiempo);

				//Si el concepto tiene una duración máxima, se suman los minutos de esa duración al tiempo trabajado:
				}elseif($row->max > 0){
					$horasDia = App\Helpers\helpers::sumaHoras($horasDia, App\Helpers\helpers::getTimeFromMinutes($row->max));
				}

				$k++;
				@endphp

				<tr>
					<td>{{ $i }}</td>
					<td>
						{{-- {{ $row->created_at->format($languages[session()->get('locale')][4]) }} --}}
						{{ \Carbon\Carbon::parse($row->date)->format($languages[session()->get('locale')][4]) }}

					</td>
					<td>
						@php
						//Término a pintar:
						//$hour_ = $row->flag == 1 && $acaba? $acaba:$row->hour;
						$p = explode(':', $row->hour);
						@endphp
						{{ $p[0] }}:{{ $p[1] }}h.
					</td>
					<td>{{ $row->name }}</td>
				</tr>

				@php
				$i++;
				@endphp
			@endforeach
		</tbody>
	</table>
</main>

</body>
</html>
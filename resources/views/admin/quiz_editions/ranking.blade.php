@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		{{-- Jugar --}}
		<a href="{{ route('quiz-editions.show', $edition) }}" class="btn btn-info">
			<i class="fa fa-play"></i>
			{!! trans('textos.jugar') !!}
		</a>
	</div>
</div>

<div class="row">
	<div class="col-6">						
		<div class="card mb-3">
			<div class="card-body">
				<h3>Resultados de la jornada</h3>

				{{-- Puntuación del usuario --}}
				<div class="row">
					<div class="col-lg-6">
						<div class="widget yellow-bg p-lg text-center">
							<div class="m-b-md">
								<h3>Preguntas</h3>
								<h1>@if($day_user_results) {{ $day_user_results->Answers }} @else 0 @endif</h1>
							</div>
						</div>
					</div>

					<div class="col-lg-6">
						<div class="widget yellow-bg p-lg text-center">
							<div class="m-b-md">
								<h3>Puntos</h3>
								<h1>@if($day_user_results) {{ $day_user_results->Points }} @else 0 @endif</h1>
							</div>
						</div>
					</div>
				</div>	
			</div>
		</div>
	</div>

	<div class="col-6">						
		<div class="card mb-3">
			<div class="card-body">
				<h3>Ranking general</h3>

				<p class="mb-0">En estos momentos ocupas la posición nº <b id="myPosition"></b></p>

				{{-- Ranking general --}}
				<div class="table-responsive">
					<table class="table table-hover" id="tblRanking">
						<thead>
							<tr>
								<th class="text-center" style="width: 40px;">#</th>
								<th>{!! ucfirst(trans('textos.usuario')) !!}</th>
								<th class="text-center">{!! ucfirst(trans('textos.preguntas_respondidas')) !!}</th>
								<th class="text-center">{!! ucfirst(trans('textos.puntos')) !!}</th>
							</tr>
						</thead>

						@php
						$i = 1;
						$my_position = false;
						@endphp

						<tbody>
							@foreach($general_ranking as $gr)
								<tr>
									<td class="text-right @if($gr->id == Auth::user()->id) bg-success @endif">{{ $i }}</td>
									<td class=" @if($gr->id == Auth::user()->id) bg-success @endif">{{ $gr->name }} {{ $gr->surname }}</td>			
									<td class="text-right @if($gr->id == Auth::user()->id) bg-success @endif">{{ $gr->Answers }}</td>		
									<td class="text-right @if($gr->id == Auth::user()->id) bg-success @endif">{{ $gr->Points }}</td>
								</tr>

								@php
								//Posición del usuario:
								if($gr->id == Auth::user()->id) $my_position = $i;

								$i++;
								@endphp
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	$('#tblRanking').DataTable({
		dom: '',
		buttons: []
	});

	//Posición del usuario:
	$('#myPosition').text({{ $my_position }});
});
</script>
@endpush

@endsection
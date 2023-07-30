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
				{{-- Puntuación del usuario --}}

			</div>
		</div>
	</div>

	<div class="col-6">						
		<div class="card mb-3">
			<div class="card-body">
				<h3>Ranking general</h3>

				{{-- Ranking general --}}
				<div class="table-responsive">
					<table class="table table-hover" id="tblRanking">
						<thead>
							<tr>
								<th>#</th>
								<th>{!! ucfirst(trans('textos.usuario')) !!}</th>
								<th class="text-center">{!! ucfirst(trans('textos.preguntas_respondidas')) !!}</th>
								<th class="text-center">{!! ucfirst(trans('textos.puntos')) !!}</th>
							</tr>
						</thead>

						@php
						$i = 1;
						@endphp

						<tbody>
							@foreach($general_ranking as $gr)
								<tr>
									<td class="text-right">{{ $i }}</td>
									<td>{{ $gr->name }} {{ $gr->surname }}</td>			<td class="text-right">{{ $gr->Answers }}</td>		
									<td class="text-right">{{ $gr->Points }}</td>
								</tr>

								@php
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
$('#tblRanking').DataTable({
	dom: '',
	buttons: []
});
</script>
@endpush

@endsection
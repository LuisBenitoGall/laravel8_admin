<div class="ibox px-3">
	@if($observations->count())
		@php
		$i = 0;
		@endphp

		@foreach($observations as $row)
			<div class="timeline-item">
				<div class="row">
					<div class="col-sm-3 col-md-2 date_">
						{{-- Eliminar comentario --}}
						<a href="{{ route('staff-pick-observations.destroy', $row->id) }}" class="btn-confirm" data-msg="{{ trans('textos.comentario_confirm_eliminar') }}" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}">
							<i class="fa fa-trash text-danger"></i>
						</a>

						{{ $row->created_at->format($languages[session()->get('locale')][4].' H:i:s'); }}

						@if($row->meeting_date)
							<b>{{ trans('textos.fecha_entrevista') }}</b> 
						@endif
					</div>

					<div class="col-sm-7 col-md-10 content @if($i==0) no-top-border @endif">
						<p>{!! $row->observations !!}</p>
					</div>
				</div>
			</div>

			@php
			$i++;
			@endphp
		@endforeach
	@else
		<h2 class="mb-5 mt-5 text-center text-warning">
            {!! trans('textos.candidato_sin_comentarios') !!}
        </h2>
	@endif
</div>
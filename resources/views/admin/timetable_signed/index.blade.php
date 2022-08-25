@extends('admin.partials.layout')

@section('content')

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('timetable-signed.list') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Año --}}
						<label for="year" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.any')) !!} </label>
						<div class="col-lg-1">
							<select name="year" class="form-control select2">
								@for($i=(date('Y')+1); $i>=config('constants.MIN_YEAR_'); $i--)
									<option value="{{ $i }}" @if($i == $year) selected @endif>{{ $i }}</option>
								@endfor	
							</select>
						</div>

						<x-btn-submit name="submit_search" class="ml-3" label="{{ trans('textos.buscar') }}" inline="true"/>

						<div class="col-md-3">
							<p class="text-warning" style="line-height: 34px; margin-bottom: 0;">{!! trans('textos.resultados_anuales_firmas') !!}.</p>	
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<div class="row">
					<div class="col-lg-12 mb-3">
						<h3><u>{{ ucfirst(trans('textos.horarios')) }} {{ $year }}</u></h3>	
					</div>

					@foreach($months as $i => $m)
						{{-- Sólo para meses y años anteriores a la fecha actual --}}
						@if(strtotime($year.'-'.$i.'-31') < strtotime(date('Y-m-d')))
							<div class="col-6 col-lg-4 col-xl-3">
								<div class="card text-white bg-info mb-3" style="max-width: 20rem;">
								  	<div class="card-body">
										<h4 class="card-title">{{ ucfirst($m) }} {{ $year }}</h4>

										@if(in_array($i, $timetables))
											<span>
												<b>{{ trans('textos.firma_horarios_validada') }}</b>
												<i class="fa fa-check ml-2" style="color: #FFF;"></i>
											</span>
										@else
											<a href="{{ route('timetable-signed.validate', [$year, $i]) }}" class="btn btn-light btn-confirm" data-msg="{{ trans('textos.firma_horarios_validar_confirm') }}">
												<i class="fa fa-calendar mr-1"></i>
												{!! trans('textos.validar_firma_horarios') !!}
											</a>
										@endif
								  	</div>
								</div>
							</div>
						@endif
					@endforeach
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
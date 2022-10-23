@extends('admin.partials.layout')

@section('content')

@if(!$user->signature)
	<div class="alert alert-danger" role="alert">
		{{ trans('textos.firma_horarios_aviso') }} <a href="{{ route('users.edit', $user->id) }}">{{ trans('textos.aqui') }}</a>.   
	</div>
@endif

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		<a href="{{ route('users.timetables', Auth::user()->id) }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_horarios_usuario') !!}
		</a>
	</div>
</div>

<div class="row">
	@if($timetables->count() > 0)
		<div class="col-sm-12">						
			<div class="card mb-3">
				<div class="card-body">
					<div class="row">
						<div class="col-lg-12 mb-3">
							<h3><u>{{ trans('textos.horarios_x_mes') }}</u></h3>	
						</div>

						@foreach($timetables as $row)
							<div class="col-4 col-lg-2 col-xl-1">
								<div class="card text-white bg-success mb-3">
								  	<div class="card-body text-center">
										<h6 class="text-center" style="line-height: 150%;">{{ ucfirst(App\Helpers\helpers::yearMonths($row->month)) }}<br>{{ $row->year }}</h6>	

										{{-- Revisar --}}
										@if($row->doc && file_exists('docs/timetables_signed/'.$row->doc))
											<a target="_blank" href="{{ asset('docs/timetables_signed/'.$row->doc) }}" class="btn btn-info btn-sm" rel="tooltip" title="{{ ucfirst(trans('textos.documento_ver')) }}">
										@else
											<a href="{{ route('timetable-signed.download', $row->id) }}" class="btn btn-info btn-sm close-animation" rel="tooltip" title="{{ ucfirst(trans('textos.revisar')) }}">
										@endif
											<i class="fa fa-calendar-alt"></i>
										</a>

										{{-- Firmar --}}
										@if(!$row->signed && $user->signature)
											<a href="{{ route('timetable-signed.sign', $row->id) }}" class="btn btn-warning btn-sm ml-2" rel="tooltip" title="{{ ucfirst(trans('textos.firmar')) }}">
												<i class="fa fa-file-signature"></i>
											</a>
										@endif
									</div>
								</div>
							</div>
						@endforeach
					</div>
				</div>
			</div>
		</div>

	@else
		<div class="col-12 text-center mt-5 pt-5">
			<h2 class="text-warning">{!! trans('textos.sin_resultados') !!}</h2>
		</div>
	@endif
</div>

@endsection
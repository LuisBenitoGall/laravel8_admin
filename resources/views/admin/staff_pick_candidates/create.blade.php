@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Listado de procesos --}}
		@can('staff-pick.edit')
			<a href="{{ route('staff-pick.edit', $process) }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_proceso') !!}
			</a>
		@endcan
	</div>
</div>

@if($process)
	<div class="row mb-3">
		<div class="col-12">
			<div class="card mb-3">
				<div class="card-body">
					<h3 class="mb-4">
						{!! ucfirst(trans('textos.candidato_nuevo')) !!} 
						<u>
							@if($process->name)
								{{ $process->name }}
							@else
								{{ $process->id }}
							@endif
						</u>
					</h3>

					<form method="post" autocomplete="off" action="{{ route('staff-pick-candidates.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
						{{ csrf_field() }}
						<input type="hidden" name="process_id" value="{{ $process->id }}">

						<div class="form-group row">
							{{-- Nombre usuario --}}
							<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nombre')) !!}*</label>
							<div class="col-lg-4">
								<x-input type="text" name="name" id="name" placeholder="{!! ucfirst(trans('textos.nombre')) !!}" autocomplete="off" maxlength="75" required/>
							</div>

							{{-- Apellidos --}}
							<label for="surname" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.apellidos')) !!}*</label>
							<div class="col-lg-4">
								<x-input type="text" name="surname" id="surname" placeholder="{!! ucfirst(trans('textos.apellidos')) !!}" autocomplete="off" maxlength="100" required/>
							</div>
						</div>

						<div class="form-group row">
							{{-- Email --}}
							<label for="email" class="col-lg-2 col-form-label">Email*</label>
							<div class="col-lg-4">
								<x-input type="email" name="email" id="email" placeholder="{!! ucfirst(trans('textos.email')) !!}" autocomplete="off" maxlength="100" required/>
							</div>

							{{-- Fecha entrevista --}}
							<label for="meeting_date" class="col-lg-2 col-form-label">{{ trans('textos.fecha_entrevista') }}</label>
							<div class="col-lg-4">
								<x-date-picker name="meeting_date" :format="$languages[config('app.locale')][4]"/>
							</div>
						</div>

						<div class="form-group row">
							{{-- Imagen --}}
							<label for="image" class="col-lg-2 col-form-label">{{ ucfirst(trans('textos.foto')) }}</label>
							<div class="col-lg-4">
								<x-input-file name="image"/>

								{!! App\Helpers\helpers::info('candidate-image') !!}
							</div>

							{{-- CV --}}
							<label for="doc" class="col-lg-2 col-form-label">CV</label>
							<div class="col-lg-4">
								<x-input-file name="doc"/>

								{!! App\Helpers\helpers::info('candidate-cv') !!}
							</div>
						</div>

						<div class="form-group row">
							{{-- Observaciones --}}
							<label for="observations" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.observaciones')) !!}</label>
							<div class="col-lg-10">
								<x-input-textarea class="editor" name="observations"/>
							</div>
						</div>

						<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
					</form>
				</div>
			</div>	
		</div>
	</div>
@endif

@endsection
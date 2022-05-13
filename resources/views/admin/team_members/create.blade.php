@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		<a href="{{ route('teams.edit', $team) }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_a') !!} <b>{{ $team->name }}</b>
		</a>
	</div>
</div>
		
<div class="row mb-3">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-4">
					{!! ucfirst(trans('textos.miembro_nuevo_de')) !!} 
					<u>{{ $team->name }}</u>
				</h3>

				<form method="post" autocomplete="off" action="{{ route('team-members.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}
					<input type="hidden" name="team_id" value="{{ $team->id }}">

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

						{{-- Líder: uno por equipo --}}
						@if(!$leader)
							<label for="leader" class="col-lg-2 col-form-label">{{ trans('textos.lider') }}</label>
							<div class="col-lg-1">
								<label class="custom-chk-rdb" rel="tooltip">
					                <input type="checkbox" name="leader">
					                <span class="chk" style="top: 5px;"></span>
					            </label>

					            <span class="relativo ml-2">
					            	{!! App\Helpers\helpers::info('member-leader') !!}
					            </span>
							</div>
						@endif
					</div>
					
					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>	
	</div>
</div>

@endsection
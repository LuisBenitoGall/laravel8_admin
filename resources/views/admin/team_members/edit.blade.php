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
					{!! ucfirst(trans('textos.miembro_editar_de')) !!} 
					<u>{{ $team->name }}</u>
				</h3>

				<form method="post" autocomplete="off" action="{{ route('team-members.update', $member) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}
					<input type="hidden" name="user_id" value="{{ $user->id }}">

					<div class="form-group row">
						{{-- Nombre usuario --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nombre')) !!}*</label>
						<div class="col-lg-4">
							<x-input type="text" name="name" id="name" value="{!! $user->name !!}" autocomplete="off" maxlength="75" required/>
						</div>

						{{-- Apellidos --}}
						<label for="surname" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.apellidos')) !!}*</label>
						<div class="col-lg-4">
							<x-input type="text" name="surname" id="surname" value="{!! $user->surname !!}" autocomplete="off" maxlength="100" required/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Email --}}
						<label for="email" class="col-lg-2 col-form-label">Email*</label>
						<div class="col-lg-4">
							<x-input type="email" name="email" id="email" value="{!! $user->email !!}" autocomplete="off" maxlength="150" required/>
						</div>
					</div>
					
					<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>	
	</div>
</div>

@endsection
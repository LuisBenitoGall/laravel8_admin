@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Edición usuario --}}
		<a href="{{ route('users.edit', $user) }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_editar_usuario') !!}
		</a>

	</div>
</div>

{{-- Filtro formulario: --}}
<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('work-user-calendar.calendar-by') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}
					<input type="hidden" name="user_id" value="{{ $user->id }}">

					<div class="form-group row">
						{{-- Año --}}
						<label for="year" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.any')) !!} </label>
						<div class="col-lg-2">
							@php
                            $years = [];
                            for($y=config('constants.MIN_YEAR_'); $y<=(date('Y')+3); $y++){
                                $years[$y] = $y;
                            }
                            @endphp

                            <x-select name="year" class="select2" id="calendarYear" :options="$years" value="{{ $year }}"/>
                        </div>

                        <x-btn-submit name="submit_save" label="{{ trans('textos.buscar') }}" inline="true"/>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

{{-- Calendario --}}
<div class="row mb-3">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-3">
					{!! ucfirst(trans('textos.calendario')) !!} 
					<u>{{ $user->name }} {{ $user->surname }}, {{ $year }}</u>
				</h3>

				{{-- Component Calendario --}}
				{{-- @livewire('calendar', ['year' => $year, 'workplace' => false]) --}}
			</div>
		</div>
	</div>
</div>

@endsection
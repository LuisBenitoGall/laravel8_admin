@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Nueva agenda --}}
		@can('schedules.create')
    		<a href="{{ route('schedules.create') }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.agenda_nueva')) !!}
    		</a>
        @endcan

        {{-- Tipos de evento --}}
        <a href="{{ route('schedule-event-types.index') }}" class="btn btn-info">
			<i class="fa fa-calendar-week"></i>
			{!! ucfirst(trans('textos.eventos_tipo')) !!}
		</a>
	</div>
</div>

{{-- Componente Agenda --}}
@livewire('schedule-component')

@endsection
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

		{{-- Validar jornadas --}}
		@can('employees.edit')
			<a class="btn btn-info" href="#" data-toggle="modal" data-target="#modal-validate-days">
				<i class="fa fa-check"></i>
				{{ trans('textos.jornadas_validar') }}
			</a>
		@endcan
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
				@livewire('calendar', ['year' => $year, 'workplace' => $workplace, 'user' => $user])
			</div>
		</div>
	</div>
</div>

{{-- Modals --}}
{{-- Modificar valores tipo de día --}}
@include('admin.work_user_calendar.partials.modal-modify-day-type')

{{-- Validar jornadas --}}
@include('admin.work_user_calendar.partials.modal-validate-days')

{{-- Adjuntar archivo --}}
@include('admin.work_user_calendar.partials.modal-add-file')

@push('scripts')
<script>
$(function(){
	// Modal modificar cantidad de días por tipo de jornada.
    $('.btnModifiable').on('click', function(){
        var user_id = $('#modifiableUserId').val();
        var year = $('#modifiableYear').val();
        var daytype = $(this).data('daytype');
        var id = $(this).data('id');
        var qty = $(this).data('qty');
        var url = '/admin/day-types-users-config/'+user_id+'/'+id+'/'+year+'/destroy';

        $('#modal-modify-day-type .modal-title span').text(daytype);
        $('#modifiableDayTypeId').val(id);
        $('#modifiableQty').val(qty);
        $('.btnDeleteModified').attr('href', url);
    });

    // Seleccionar colección de checkbox.
    $('.chkSelectAll').on('click', function(){
        if($(this).is(':checked')){
            $('.chkForSelecting').prop('checked', true);
        }else{
            $('.chkForSelecting').prop('checked', false);
        }
    });
});
</script>
@endpush

@endsection
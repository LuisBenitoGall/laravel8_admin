@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Volver a agendas --}}
		<a href="{{ route('schedules.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_agendas') !!}
		</a>

		{{-- Nueva agenda --}}
		@can('schedules.create')
    		<a href="{{ route('schedules.create') }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.agenda_nueva')) !!}
    		</a>
        @endcan
	</div>
</div>

<div class="row mb-3">
	<div class="col-xs-12 col-sm-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('schedules.update', $schedule->id) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Agenda --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.agenda')) !!}*</label>
						<div class="col-lg-4">
							<x-input type="text" name="name" id="name" value="{{ $schedule->name }}" autocomplete="off" maxlength="255" required/>
						</div>

						{{-- Color --}}
						<label for="color" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.color')) !!}</label>
						<div class="col-lg-2">
							<div class="input-group m-b">
								<div class="input-group-prepend">
                    				<span class="input-group-addon" @if($schedule->color) style="background-color: {{ $schedule->color }}" @endif>&nbsp;</span>
                				</div>
								<x-color-picker name="color" id="colorpicker_schedule" value="{{ $schedule->color }}"/>
							</div>
						</div>

						{{-- Status --}}
						<label for="status" class="col-lg-1 col-form-label">{!! ucfirst(trans('textos.activo')) !!}</label>
						<div class="col-lg-1">
							<label class="custom-chk-rdb" rel="tooltip">
				                <input type="checkbox" name="status" @if($schedule->status == 1) checked @endif>
				                <span class="chk" style="top: 5px;"></span>
				            </label>
						</div>
					</div>

					<div class="form-group row">
						{{-- Descripción --}}
						<label for="description" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.descripcion')) !!}</label>
						<div class="col-lg-10">
							<x-input-textarea name="description" value="{{ $schedule->description }}"/>
						</div>
					</div>

					<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>	
		</div>
	</div>
</div>

@endsection

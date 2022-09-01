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
	</div>
</div>

<div class="row mb-3">
	<div class="col-xs-12 col-sm-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('schedules.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Agenda --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.agenda')) !!}*</label>
						<div class="col-lg-4">
							<x-input type="text" name="name" id="name" autocomplete="off" maxlength="255" required/>
						</div>

						{{-- Color --}}
						<label for="color" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.color')) !!}</label>
						<div class="col-lg-2">
							<div class="input-group m-b">
								<div class="input-group-prepend">
                    				<span class="input-group-addon">&nbsp;</span>
                				</div>
								<x-color-picker name="color" id="colorpicker_schedule"/>
							</div>
						</div>

						{{-- Status --}}
						<label for="status" class="col-lg-1 col-form-label">{!! ucfirst(trans('textos.activo')) !!}</label>
						<div class="col-lg-1">
							<label class="custom-chk-rdb" rel="tooltip">
				                <input type="checkbox" name="status" checked>
				                <span class="chk" style="top: 5px;"></span>
				            </label>
						</div>
					</div>

					<div class="form-group row">
						{{-- Descripción --}}
						<label for="description" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.descripcion')) !!}</label>
						<div class="col-lg-10">
							<x-input-textarea name="description"/>
						</div>
					</div>

					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>	
		</div>
	</div>
</div>

@endsection
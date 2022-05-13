@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		<a href="{{ route('teams.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_equipos') !!}
		</a>
	</div>
</div>
		
<div class="row mb-3">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('teams.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Nombre equipo --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nombre')) !!}*</label>
						<div class="col-lg-6">
							<x-input type="text" name="name" id="name" placeholder="{!! ucfirst(trans('textos.equipo')) !!}" autocomplete="off" maxlength="75" required/>
						</div>
						
						{{-- Status --}}
						<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.activo')) !!}</label>
						<div class="col-lg-1">
							<label class="custom-chk-rdb" rel="tooltip">
				                <input type="checkbox" name="status" checked>
				                <span class="chk" style="top: 5px;"></span>
				            </label>

				            <span class="relativo ml-2">
				            	{!! App\Helpers\helpers::info('user-activo') !!}
				            </span>
						</div>
					</div>

					<div class="form-group row">
                        {{-- Logo --}}
                        <label for="name" class="col-md-2 col-form-label">Logo
							<br>
							<small class="text-warning">
								· {{ trans('textos.formato') }}: .jpg, .png, .gif <br>
								· {{ trans('textos.tamany_max') }}: 1MB	<br>
								· {{ trans('textos.medidas_recomendadas') }}: 400x400px							
							</small>
						</label>
						<div class="col-md-4">
							<x-input-file name="logo"/>

							@if($errors->has('logo'))
			                    <span class="invalid-feedback" role="alert">
			                      <strong>{{ $errors->first('logo') }}</strong>
			                    </span>
			                @endif
						</div>
					</div>
					
					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>	
	</div>
</div>

@endsection
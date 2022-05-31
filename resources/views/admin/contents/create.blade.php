@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		<a href="{{ route('contents.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_contenidos') !!}
		</a>
	</div>
</div>

<div class="row">
	<div class="col-xs-12 col-sm-12">
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('contents.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Nombre contenido --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.contenido_nombre')) !!}*</label>
						<div class="col-lg-10">
							<x-input type="text" name="name" id="name" placeholder="{!! ucfirst(trans('textos.contenido_nombre')) !!}" autocomplete="off" maxlength="150" required warning="contenido_nombre_explain"/>

							@if($errors->has('code'))
                                <span class="invalid-feedback" role="alert">
                                  	<strong>{{ $errors->first('code') }}</strong>
                                </span>
                            @endif
						</div>
					</div>

					<div class="form-group row">
						{{-- Estado --}}
						<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.contenido_activo')) !!}</label>
						<div class="col-lg-1">
							{{-- <x-checkbox name="status" title="contenido_on_off" checked style="top: 5px;"/> --}}

							<label class="custom-chk-rdb" rel="tooltip" title="{!! trans('textos.contenido_on_off') !!}" 
						    >
						        <input type="checkbox" name="status" checked>
						        <span class="chk" style="top: 5px;"></span>
						    </label>  
	                    </div>

	                    {{-- Tipo --}}
						<label for="type" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.tipo')) !!} *</label>
						<div class="col-lg-2">
							<x-radio name="type" value="1" title="informacion" required style="margin-bottom: 0; margin-top: 7px;"/>
						</div>

						<div class="col-lg-2">
							<x-radio name="type" value="2" title="contenido" required style="margin-bottom: 0; margin-top: 7px;"/>

							{!! App\Helpers\Helpers::info('contents-tipo') !!}
						</div>
					</div>
					
					<x-btn-submit name="submit_save" label="continuar"/>
				</form>
			</div>							
		</div>
    </div>		
</div>

@endsection
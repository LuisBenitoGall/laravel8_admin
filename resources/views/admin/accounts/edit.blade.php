@extends ('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-sm-12">
		<a href="{{ route('accounts.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_cuentas') !!}
		</a>
	</div>
</div>

<div class="row">
	<div class="col-xs-12 col-sm-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('accounts.update', $account->id) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Nombre cuenta --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cuenta')) !!} *</label>
						<div class="col-lg-10">
							<x-input type="text" name="name" id="name" placeholder="{!! ucfirst(trans('textos.cuenta')) !!}" autocomplete="off" maxlength="150" required value="{{ $account->name }}" />
						</div>	
					</div>

					<div class="form-group row">
						{{-- Tarifa --}}
						<label for="rate" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.tarifa')) !!}</label>
						<div class="col-lg-2">
							<x-input type="text" name="rate" class="text-right" id="rate" autocomplete="off" maxlength="9" group="right" groupvalue="{{ config('constants.CURRENCY_') }}" value="{{ $account->rate }}"/>
						</div>	

						{{-- Duración --}}
						<label for="duration" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.duracion')) !!} <small class="text-warning">({!! trans('textos.dias') !!})</small></label>
						<div class="col-lg-2">
							<x-input type="text" name="duration" class="text-right" id="duration" placeholder="0" autocomplete="off" maxlength="9" value="{{ $account->duration }}"/>
						</div>	

						{{-- Status --}}
						<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.activo')) !!}</label>
						<div class="col-lg-2">
							<label class="custom-chk-rdb" rel="tooltip" title="{!! trans('textos.sede_on_off') !!}">
	                            <input type="checkbox" name="status" @if($account->status == 1) checked @endif>
	                            <span class="chk" style="top: 5px;"></span>
	                        </label>
						</div>
					</div>

					<div class="form-group row">
						{{-- Descripción --}}
						<label for="description" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.descripcion')) !!}</label>
						<div class="col-lg-10">
							<x-input-textarea name="description" value="{{ $account->description }}"/>
						</div>
					</div>

					<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection
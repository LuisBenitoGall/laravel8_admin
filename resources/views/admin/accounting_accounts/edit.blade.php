@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-sm-12">
		<a href="{{ route('accounting-accounts.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_cuentas_contables') !!}
		</a>

		{{-- Nueva cuenta --}}
		@can('accounting-accounts.create')
			<a class="btn btn-info" href="{{ route('accounting-accounts.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.cuenta_nueva')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-xs-12 col-sm-12">						
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-3">
					{{ trans('textos.cuenta_contable') }} <b>{{ $account->name }}</b>
				</h3>

				{{-- Info --}}
				<div class="mb-4">
					{{-- Fecha alta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.fecha_alta')) !!}: 
						<b>
							{{ $account->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Fecha última actualización --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.ultima_edicion')) !!}: 
						<b>
							{{ $account->updated_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Tipo de cuenta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.cuenta_tipo')) !!}:
						@php
						$type_ = substr($account->type, 0, 1);
						$type = $types[$type_];

						$subtype_ = substr($account->type, 1,2);
						$subtype = isset($subtypes[$type_][$subtype_])? $subtypes[$type_][$subtype_]:false;
						@endphp

						<b>{{ $type }}@if($subtype) - {{ $subtype }} @endif</b>
					</label>

					{{-- Aplica IVA --}}
					<label class="mr-5">
						{{ trans('textos.iva_aplica') }}:
						<b>
							@if($account->with_iva)
								<i class="fa fa-check text-success ml-2"></i>
							@else
								<i class="fa fa-ban text-danger ml-2"></i>	
							@endif
						</b>
					</label>
				</div>

				<form method="post" autocomplete="off" action="{{ route('accounting-accounts.update', $account) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Cuenta --}}
						<label for="account" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cuenta')) !!}*</label>
						<div class="col-lg-3">
							<x-input type="text" name="account" placeholder="{!! trans('textos.cuenta') !!}" autocomplete="off" required value="{{ $account->account }}" readonly/>
						</div>

						{{-- Nombre --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nombre')) !!}*</label>
						<div class="col-lg-3">
							<x-input type="text" name="name" placeholder="{!! trans('textos.nombre') !!}" autocomplete="off" required value="{{ $account->name }}"/>
						</div>

						{{-- Status --}}
						<label for="status" class="col-lg-1 col-form-label">{!! ucfirst(trans('textos.activo')) !!}</label>
						<div class="col-lg-1">
							<label class="custom-chk-rdb" rel="tooltip" title="{!! trans('textos.sede_on_off') !!}">
								<input type="checkbox" name="status" @if($account->status == 1) checked @endif>
								<span class="chk" style="top: 5px;"></span>
							</label>
						</div>
					</div>	 

					<div class="form-group row">
						{{-- Tipo --}}
						{{-- <label for="type" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.tipo')) !!}*</label>
						<div class="col-lg-3">
							<x-select name="type" class="select2" :options="$types" label="tipo_selec" required value="{{ $account->type }}" disabled/>
						</div> --}}

						{{-- Empresa --}}
						<label for="company" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.empresa')) !!}</label>
						<div class="col-lg-5 position-relative">
							<x-input type="text" name="company" autocomplete="off" required value="{{ $company->name }}" disabled/>
						</div> 
					</div> 
		
					<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection
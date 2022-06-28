@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		<a href="{{ route('projects.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_proyectos') !!}
		</a>
	</div>
</div>

<div class="row mb-3">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('projects.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Nombre proyecto --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.proyecto')) !!}*</label>
						<div class="col-lg-10">
							<x-input type="text" name="name" id="name" placeholder="{!! ucfirst(trans('textos.nombre')) !!}" autocomplete="off" maxlength="255" required/>
						</div>
					</div>

					{{-- Selecciona cliente --}}
					<div class="form-group row">
						<label for="customer_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cliente')) !!}</label>
						<div class="col-lg-4">
							@php
							$customers_ = [];
							@endphp

							@foreach($customers as $ctm)
								@php
								$company = $ctm->tradename? $ctm->name.' ('.$ctm->tradename.')':$ctm->name;
								$customers_[$ctm->id] = $company;
								@endphp
							@endforeach

							<x-select name="customer_id" id="formCustomerId" class="select2" label="selec_cliente" :options="$customers_" info="project-customer"/>
						</div>
					</div>
					
					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection
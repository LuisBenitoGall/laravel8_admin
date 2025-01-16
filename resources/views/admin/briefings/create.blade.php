@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-sm-12">
		{{-- Volver a briefings --}}
		<a href="{{ route('briefings.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_briefings') !!}
		</a>
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('briefings.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Nombre --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.titulo')) !!}*</label>
						<div class="col-lg-4">
							<x-input type="text" name="name" placeholder="{!! ucfirst(trans('textos.titulo')) !!}" autocomplete="off" maxlength="150" required/>
						</div>	

						{{-- Plantilla --}}
						<label for="template" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.plantilla')) !!}</label>
						<div class="col-lg-4">
							<x-select name="template" class="select2" label="plantilla_selec" :options="$templates"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Clientes --}}
						<label for="customer" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cliente')) !!}</label>
						<div class="col-md-4 position-relative">
   			            	<x-input type="text" name="customer" id="search_customer" placeholder="{!! ucfirst(trans('textos.cliente')) !!}" maxlength="150"/>
    			        </div>

    			        <input type="hidden" name="customer_id" id="customerId">
					</div>

					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection
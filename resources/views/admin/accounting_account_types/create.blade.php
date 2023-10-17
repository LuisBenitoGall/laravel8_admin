@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-sm-12">
		<a href="{{ route('accounting-account-types.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_grupos_contables') !!}
		</a>
	</div>
</div>

<div class="row">
	<div class="col-xs-12 col-sm-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('accounting-account-types.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Código --}}
						<label for="code" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.codigo')) !!}*</label>
						<div class="col-lg-2 position-relative">
							<x-input type="text" name="code" placeholder="{!! ucfirst(trans('textos.codigo')) !!}" maxlength="2" required/>
						</div> 

						{{-- Nombre --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nombre')) !!}*</label>
						<div class="col-lg-6 position-relative">
							<x-input type="text" name="name" placeholder="{!! ucfirst(trans('textos.nombre')) !!}" maxlength="150" required/>
						</div> 
					</div>
		
					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection
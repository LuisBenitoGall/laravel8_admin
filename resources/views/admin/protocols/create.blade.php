@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Volver a protocolos --}}
		<a href="{{ route('protocols.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_protocolos') !!}
		</a>	
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('protocols.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Nombre --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nombre')) !!}</label>
						<div class="col-lg-10">
							<x-input type="text" name="name" placeholder="{!! ucfirst(trans('textos.nombre')) !!}" maxlength="150"/>
						</div>
					</div>

					<div class="form-group row">		
						{{-- Descripción --}}
						<label for="description" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.descripcion')) !!}</label>
						<div class="col-lg-10">
							<x-input-textarea name="description" class="editor"/>
						</div>	
					</div>

					<x-btn-submit name="submit_save" classdiv="col-12 text-right" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection
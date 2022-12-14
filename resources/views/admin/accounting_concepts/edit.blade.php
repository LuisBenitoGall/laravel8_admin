@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		<a href="{{ route('accounting-concepts.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_conceptos') !!}
		</a>

		@can('accounting-concepts.create')
			<a class="btn btn-info" href="{{ route('accounting-concepts.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.concepto_nuevo')) !!}</a>
		@endcan
	</div>
</div>

<div class="row mb-3">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-4">
					{{ ucfirst(trans('textos.concepto')) }} 
					<u>{{ ucfirst($concept->name) }}</u>
				</h3>

				<form method="post" autocomplete="off" action="{{ route('accounting-concepts.update', $concept) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Nombre concepto --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.concepto')) !!}*</label>
						<div class="col-lg-6">
							<x-input type="text" name="name" maxlength="150" placeholder="{!! ucfirst(trans('textos.categoria')) !!}" autocomplete="off" required info="categories-name" value="{{ $concept->name }}"/>
						</div>	

						{{-- Status --}}
						<label for="status" class="col-lg-1 col-form-label">{!! ucfirst(trans('textos.activo')) !!}</label>
						<div class="col-lg-1">
							<label class="custom-chk-rdb" rel="tooltip">
				                <input type="checkbox" name="status" @if($concept->status == 1) checked @endif>
				                <span class="chk" style="top: 5px;"></span>
				            </label>
						</div>
					</div>

					<div class="form-group row">
						{{-- Conceptos generales --}}
                        <label for="main" class="col-lg-2 col-form-label">
                        	{!! ucfirst(trans('textos.conceptos_generales')) !!}
                        	<br>
                        	<small class="text-warning">{{ trans('textos.conceptos_generales_texto') }}</small>
                        </label>
                        <div class="col-lg-4">
							<x-select name="main" class="select2" :options="$concepts" label="conceptos_generales_selec" value="{{ $concept->main }}"/>
                        </div>
                        <div class="col-lg-1"></div>
					</div>

					<div class="form-group row">
                    	{{-- Descripción --}}
						<label for="description" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.descripcion')) !!}</label>
						<div class="col-lg-10">
							<x-input-textarea name="description" class="editor" value="{!! $concept->description !!}"/>
						</div>
                    </div>

					<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection
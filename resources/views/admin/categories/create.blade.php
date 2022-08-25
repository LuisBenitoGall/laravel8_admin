@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		<a href="{{ route($entity.'.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_categorias') !!}
		</a>
	</div>
</div>
		
<div class="row mb-3">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('categories.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}
					<input type="hidden" name="module_id" value="{{ $module->id }}">
					<input type="hidden" name="return" value="{{ $entity }}">

					<div class="form-group row">
						{{-- Nombre categoría --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.categoria')) !!}*</label>
						<div class="col-lg-6">
							<x-input type="text" name="name" maxlength="150" placeholder="{!! ucfirst(trans('textos.categoria')) !!}" autocomplete="off" required info="categories-name"/>
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
						{{-- Categorías principales --}}
                        <label for="category_father" class="col-lg-2 col-form-label">
                        	{!! ucfirst(trans('textos.categorias_principales')) !!}
                        	<br>
                        	<small class="text-warning">{{ trans('textos.subcategoria_categoria') }}</small>
                        </label>
                        <div class="col-lg-4">
							<x-select name="category_father" class="select2" :options="$categories" label="categoria_selec"/>
                        </div>
                        <div class="col-lg-1"></div>
					
						<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}" inline="true"/>
					</div>
				</form>
			</div>
		</div>	
	</div>
</div>

@endsection
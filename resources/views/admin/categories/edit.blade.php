@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		<a href="{{ route($entity.'.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_categorias') !!}
		</a>

		@can($permission.'.edit')
			<a class="btn btn-info" href="{{ route('categories.create', $module->id) }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.categoria_nueva')) !!}</a>
		@endcan
	</div>
</div>

<div class="row mb-3">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-4">
					{{ ucfirst(trans('textos.categoria')) }} 
					<u>{{ ucfirst($category->name) }}</u>
					({{ trans('textos.'.$module->label) }})
				</h3>

				<form method="post" autocomplete="off" action="{{ route('categories.update', $category) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}
					<input type="hidden" name="return" value="{{ $entity }}">

					<div class="form-group row">
						{{-- Nombre categoría --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.categoria')) !!}*</label>
						<div class="col-lg-6">
							<x-input type="text" name="name" maxlength="150" placeholder="{!! ucfirst(trans('textos.categoria')) !!}" autocomplete="off" required info="categories-name" value="{{ $category->name }}"/>
						</div>	

						{{-- Status --}}
						<label for="status" class="col-lg-1 col-form-label">{!! ucfirst(trans('textos.activo')) !!}</label>
						<div class="col-lg-1">
							<label class="custom-chk-rdb" rel="tooltip">
				                <input type="checkbox" name="status" @if($category->status == 1) checked @endif>
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
							<x-select name="category_father" class="select2" :options="$categories" label="categoria_selec" value="{{ $category->subcategory }}"/>
                        </div>
                        <div class="col-lg-1"></div>
					
						<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}" inline="true"/>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection
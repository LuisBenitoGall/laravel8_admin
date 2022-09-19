@extends ('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		<a href="{{ route('user-categories.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_categorias') !!}
		</a>

		<a class="btn btn-info" href="{{ route('user-categories.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.categoria_nueva')) !!}</a>
	</div>
</div>

<div class="row mb-3">
	<div class="col-xs-12 col-sm-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('user-categories.update', $category->id) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}

					<div class="form-group row">
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.categoria')) !!} *</label>
						<div class="col-lg-6">
							<input type="text" name="name" class="form-control" placeholder="{!! ucfirst(trans('textos.categoria')) !!}" autocomplete="off" required value="{{ old('name', $category->name) }}" maxlength="150">

							@if($errors->has('name'))
                                <span class="invalid-feedback" role="alert">
                                  <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
						</div>	

						<label for="status" class="col-lg-1 col-form-label">{!! ucfirst(trans('textos.activo')) !!}</label>
						<div class="col-lg-1">
							<label class="custom-chk-rdb" rel="tooltip">
				                <input type="checkbox" name="status" @if($category->status == 1) checked @endif>
				                <span class="chk" style="top: 5px;"></span>
				            </label>
						</div>
					</div>

					<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection
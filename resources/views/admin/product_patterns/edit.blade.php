@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		<a href="{{ route('product-patterns.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_patrones') !!}
		</a>

		@can('product-patterns.create')
			<a class="btn btn-info" href="{{ route('product-patterns.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.patron_nuevo')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('product-patterns.update', $pattern) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Nombre del patrón --}}
                        <label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.patron')) !!} *</label>
                        <div class="col-lg-7">
                        	<x-input type="text" name="name" autocomplete="off" value="{{ $pattern->name }}" maxlength="255" required/>
                        </div>

                        {{-- Estado --}}
						<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.patron_activo')) !!}</label>
						<div class="col-lg-1">
							<label class="custom-chk-rdb">
						        <input type="checkbox" name="status" @if($pattern->status == 1) checked @endif>
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
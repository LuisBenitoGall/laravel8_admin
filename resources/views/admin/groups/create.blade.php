@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('groups.index')
			<a href="{{ route('groups.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_grupos') !!}
			</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('groups.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Nombre grupo --}}
						<label for="name" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.grupo')) !!} *</label>
						<div class="col-md-6">
							<x-input type="text" name="name" id="name" placeholder="{!! ucfirst(trans('textos.grupo')) !!}" autocomplete="off" maxlength="100" required/>
						</div>	

						{{-- Estado --}}
						<label for="status" class="col-md-2 col-form-label text-md-right">{!! ucfirst(trans('textos.grupo_activo')) !!}</label>
                        <div class="col-md-2">
							<label class="custom-chk-rdb">
                        		<input type="checkbox" name="status">
                        		<span class="chk mt-1"></span>
                    		</label>
                        </div>
					</div>
					
					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection
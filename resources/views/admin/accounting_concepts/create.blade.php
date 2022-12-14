@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		<a href="{{ route('accounting-concepts.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_conceptos') !!}
		</a>
	</div>
</div>
		
<div class="row mb-3">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('accounting-concepts.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Nombre concepto --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.concepto')) !!}*</label>
						<div class="col-lg-6">
							<x-input type="text" name="name" maxlength="150" placeholder="{!! ucfirst(trans('textos.concepto')) !!}" autocomplete="off" required info="accounting-concept-name"/>
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
						{{-- Conceptos generales --}}
                        <label for="main" class="col-lg-2 col-form-label">
                        	{!! ucfirst(trans('textos.conceptos_generales')) !!}
                        	<br>
                        	<small class="text-warning">{{ trans('textos.conceptos_generales_texto') }}</small>
                        </label>
                        <div class="col-lg-4">
							<x-select name="main" class="select2" :options="$concepts" label="conceptos_generales_selec"/>
                        </div>
                        <div class="col-lg-1"></div>
                    </div>

                    <div class="form-group row">
                    	{{-- Descripción --}}
						<label for="description" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.descripcion')) !!}</label>
						<div class="col-lg-10">
							<x-input-textarea name="description" class="editor"/>
						</div>
                    </div>

					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>	
	</div>
</div>

@endsection
@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Volver a atributos --}}
		<a href="{{ route('attributes.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_atributos') !!}
		</a>
	</div>
</div>
		
<div class="row mb-3">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('attributes.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Nombre atributo --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.atributo')) !!}*</label>
						<div class="col-lg-6">
							<x-input type="text" name="name" maxlength="150" placeholder="{!! ucfirst(trans('textos.atributo')) !!}" autocomplete="off" required info="attributes-name"/>
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
						{{-- Tipo de dato --}}
                        <label for="datatype" class="col-lg-2 col-form-label">
                        	{!! ucfirst(trans('textos.tipo_dato')) !!}
                        </label>
                        <div class="col-lg-4">
                        	@php
                        	$data_types_ = [];
                        	foreach($data_types as $i => $dt){
                        		$data_types_[$i] = $dt[0];
                        	}
                        	@endphp

							<x-select name="datatype" class="select2" id="dataTypeSelect" :options="$data_types_" label="tipo_dato_selec" required/>
                        </div>
                    </div>

                    <div id="setValues">
	                    <div class="form-group row">
	                        {{-- Valores --}}
	                        <label for="values" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.valores')) !!}</label>

	                        {{-- Tipo Color. Sólo se muestra para este tipo de dato. --}}
	                        <div class="col-lg-2 typeColor">
		                        <div class="input-group m-b">
	                                <div class="input-group-prepend">
	                                    <span class="input-group-addon">&nbsp;</span>
	                                </div>
	                                <x-color-picker name="value_color" id="cPicker"/>

		                            <a href="#" class="btnAddColor text-info" data-toggle="modal" data-target="#modal-set-color">
		                            	<i class="fa fa-arrow-alt-circle-right"></i>
		                            </a>
	                            </div>
	                        </div>
	                        <div class="col-lg-8 typeColor">
	                        	<div class="colors" id="attributeOptions"></div>
	                        	<input type="hidden" name="values_colors" id="valuesColors">
	                        </div>

	                        {{-- Tipo Opciones --}}
			                <x-selectize name="values" class="typeOpciones col-lg-10" maxlength="1000" id="attributeOptions_" value="" autocomplete="off" style="position: relative; z-index: 0;"/>
			                <small class="text-warning offset-md-2 col-lg-6">{{ trans('textos.valores_introducir_texto') }}</small>               
	                    </div>
                    </div>

                    <div class="form-group row">
                    	{{-- Descripción --}}
						<label for="description" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.descripcion')) !!}</label>
						<div class="col-lg-10">
							<x-input-textarea name="description"/>
						</div>
					</div>
					
					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>	
	</div>
</div>

{{-- Modals: --}}
@include('admin.attributes.partials.modal-set-color')

@push('scripts')
@include('admin.attributes.partials.js-scripts')
@endpush

@endsection
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

		{{-- Nuevo atributo --}}
		@can('attributes.create')
			<a class="btn btn-info" href="{{ route('attributes.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.atributo_nuevo')) !!}</a>
		@endcan
	</div>
</div>
		
<div class="row mb-3">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-4">
					{{ ucfirst(trans('textos.atributo')) }} 
					<u>{{ ucfirst($attribute->name) }}</u>
				</h3>

				<form method="post" autocomplete="off" action="{{ route('attributes.update', $attribute) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}
					<input type="hidden" name="datatype" value="{{ $attribute->datatype }}">

					<div class="form-group row">
						{{-- Nombre atributo --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.atributo')) !!}*</label>
						<div class="col-lg-6">
							<x-input type="text" name="name" maxlength="150" placeholder="{!! ucfirst(trans('textos.atributo')) !!}" value="{{ $attribute->name }}" autocomplete="off" required info="attributes-name"/>
						</div>	

						{{-- Status --}}
						<label for="status" class="col-lg-1 col-form-label">{!! ucfirst(trans('textos.activo')) !!}</label>
						<div class="col-lg-1">
							<label class="custom-chk-rdb" rel="tooltip">
				                <input type="checkbox" name="status" @if($attribute->status == 1) checked @endif>
				                <span class="chk" style="top: 5px;"></span>
				            </label>
						</div>
					</div>

					<div class="form-group row">
						{{-- Tipo de dato --}}
                        <label for="datatype_" class="col-lg-2 col-form-label">
                        	{!! ucfirst(trans('textos.tipo_dato')) !!}
                        </label>
                        <div class="col-lg-4">
                        	@php
                        	$datatype = $data_types[$attribute->datatype][0];
                        	@endphp

                        	<x-input type="text" name="datatype_" maxlength="150" value="{{ $datatype }}" autocomplete="off" disabled/>
                        </div>
                    </div>

                    {{-- Valores --}}
                    @if($attribute->values)
	                    <div class="form-group row">
	                    	<label for="values" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.valores')) !!}</label>

	                        {{-- Tipo opciones --}}
	                        @if($data_types[$attribute->datatype][2] == 'select')
				                <x-selectize name="values" class="col-lg-10" maxlength="1000" value="{{ $attribute->values }}" autocomplete="off" style="position: relative; z-index: 0;"/>
				                <small class="text-warning offset-md-2 col-lg-6">{{ trans('textos.valores_introducir_texto') }}</small>

				            {{-- Tipo color --}}
				            @elseif($data_types[$attribute->datatype][2] == 'color')
				            	<div class="col-lg-4 col-xl-3 typeColor" style="display: block;">
			                        <div class="input-group m-b position-relative">
		                                <div class="input-group-prepend">
		                                    <span class="input-group-addon">&nbsp;</span>
		                                </div>
		                                <x-color-picker name="value_color" id="cPicker"/>

			                            <a href="#" class="btnAddColor text-info" data-toggle="modal" data-target="#modal-set-color">
			                            	<i class="fa fa-arrow-alt-circle-right"></i>
			                            </a>
		                            </div>
		                        </div>
		                        <div class="col-lg-6 col-xl-7 typeColor" style="display: block;">
		                        	<div class="colors" id="attributeOptions">
		                        		@php
		                        		$arrColors = $attribute->values? explode(' ', $attribute->values):[];
		                        		@endphp

		                        		@foreach($arrColors as $ac)
		                        			@if($ac != '')
			                        			@php
			                        			$p = explode('-', $ac);
			                        			@endphp
			                        			<span style="background-color: {{ $p[0] }};">{{ $ac }} <i class="fa fa-times ml-2"></i></span>
			                        		@endif
		                        		@endforeach
		                        	</div>
		                        	<input type="hidden" name="values_colors" id="valuesColors" value="{{ $attribute->values }}">
		                        </div>
				            @endif
	                    </div>
	                @endif

                    <div class="form-group row">
                    	{{-- Descripción --}}
						<label for="description" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.descripcion')) !!}</label>
						<div class="col-lg-10">
							<x-input-textarea name="description" value="{{ $attribute->description }}"/>
						</div>
					</div>
					
					<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
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
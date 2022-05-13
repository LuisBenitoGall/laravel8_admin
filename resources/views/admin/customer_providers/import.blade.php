@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	@php
	$url = $business_relationship == 'customers'? 'customers.index':'providers.index';
	$text = $business_relationship == 'customers'? 'volver_clientes':'volver_proveedores';
	@endphp
	<div class="col-sm-12">
		<a href="{{ route($url) }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.'.$text) !!}
		</a>
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
        		<p class="text-warning">
        			{!! trans('textos.importar_excel_texto') !!}
        			<br>
        			@if($business_relationship == 'providers')
        				{{ trans('textos.descargar_template_proveedores') }} 
        			@else
        				{{ trans('textos.descargar_template_clientes') }} 
        			@endif
        			<a href="{!! asset('downloads/template_import_companies.xlsx') !!}" class="text-info" download style="font-size: 20px;"><i class="fa fa-file-excel ml-2"></i></a>
        		</p>

				<form action="{{ route('companies.import') }}" method="post" accept-charset="utf-8" enctype="multipart/form-data" role="form">
		            {{csrf_field()}}
		            <input type="hidden" name="business_relationship" value="{{ $business_relationship }}">

		            <div class="row">
		            	<div class="col-12 col-md-6">
		            		<div class="form-group row">
		            			{{-- Archivo .xls --}}
		            			<div class="col-12">
		            				<x-input-file-xls name="companies"/>
		            			</div>

		            			<div class="col-12">
		            				<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
		            			</div>
		            		</div>	
		            	</div>

		               <div class="col-12 col-md-6">
		                	@if($withXlsErrors)
		                		<p>{{ trans('textos.errores_detectados_importacion') }}:</p>
		                		<p class="text-danger">
			                		@foreach($xls_errors as $i => $error)
			                			@if($error)
			                				{{ trans('textos.fila') }} {{ $i }} - {!! $error !!}<br>
			                			@endif
			                		@endforeach
		                		</p>
							@endif
		                </div>		
		            </div>
		        </form>
		    </div>
		</div>
	</div>
</div>

@endsection

--------------------@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		<a href="{{ route('banks.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_bancos') !!}
		</a>
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('banks.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Razón social del banco --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.banco')) !!}*</label>
						<div class="col-lg-4">
							<x-input type="text" name="name" placeholder="{!! ucfirst(trans('textos.banco')) !!}" autocomplete="off" maxlength="150" required info="company-name"/>
						</div>	

						{{-- Nombre comercial --}}
						<label for="tradename" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nombre_comercial')) !!}</label>
						<div class="col-lg-4">
							<x-input type="text" name="tradename" placeholder="{!! ucfirst(trans('textos.nombre_comercial')) !!}" autocomplete="off" maxlength="150" info="company-tradename"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- LEI --}}
						<label for="lei" class="col-md-2 col-form-label text-md-right">LEI</label>
                        <div class="col-md-4">
                        	<x-input type="text" name="lei" placeholder="LEI" autocomplete="off" maxlength="25"/>
                        </div>

                        {{-- Status --}}
						<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.estado')) !!}</label>
						<div class="col-lg-2">
							<label class="custom-chk-rdb" rel="tooltip" title="{!! trans('textos.banco_on_off') !!}">
	                            <input type="checkbox" name="status">
	                            <span class="chk" style="top: 5px;"></span>
	                        </label>
	                    </div>
                    </div>

                    <div class="form-group row">
                    	{{-- SWIFT / BIC --}}
                    	<label for="swift" class="col-md-2 col-form-label">SWIFT / BIC</label>
						<div class="col-md-2">
							<x-input type="text" name="swift"/>
						</div>

                        {{-- Código europeo --}}
                        <label for="eu_code" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.codigo_europeo')) !!}</label>
						<div class="col-md-2">
							<x-input type="text" name="eu_code"/>
						</div>

						{{-- Código supervisor --}}
                        <label for="supervisor_code" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.codigo_supervisor')) !!}</label>
						<div class="col-md-2">
							<x-input type="text" name="supervisor_code"/>
						</div>
					</div>

					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection

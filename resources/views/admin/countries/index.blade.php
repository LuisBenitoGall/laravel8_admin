@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('countries.create')
			<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-new-country"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.pais_nuevo')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblCountries">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.pais')) !!}</th>
					<th>{!! ucfirst(trans('textos.codigo')) !!}</th>
					<th>Alfa 2</th>
					<th>Alfa 3</th>
					<th class="text-center">{!! ucfirst(trans('textos.bandera')) !!} </th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

{{-- Modals --}}
{{-- New country --}}
<div class="modal fade modal-new-country" id="modal-new-country" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{!! ucfirst(trans('textos.pais_nuevo')) !!}</h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
				<form method="post" action="{{ route('countries.store') }}" autocomplete="off" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						<div class="col-lg-9">
							<div class="row">
								{{-- País --}}
								<label for="name" class="col-lg-12">{!! ucfirst(trans('textos.pais')) !!} *</label>
								<div class="col-lg-12">
									<x-input type="text" name="name" maxlength="191" required/>
								</div>
							</div>
						</div>

						<div class="col-lg-3">
							<div class="row">
								{{-- Status --}}
								<label for="status" class="col-lg-12">{!! ucfirst(trans('textos.estado')) !!}</label>
								<div class="col-lg-12 text-center">
									<label class="custom-chk-rdb">
						        		<input type="checkbox" name="status" checked>
						        		<span class="chk" style="top: 5px;"></span>
						    		</label>
								</div>
							</div>
						</div>
					</div>

					<div class="form-group row">
						{{-- Código --}}
						<div class="col-lg-4">
							<div class="row">
								<label for="code" class="col-lg-12">{!! ucfirst(trans('textos.codigo')) !!}</label>	
								<div class="col-lg-12">
									<x-input type="text" name="code" maxlength="3" info="country-code"/>
								</div>
							</div>
						</div>

						{{-- Alfa 2 --}}
						<div class="col-lg-4">
							<div class="row">
								<label for="alfa2" class="col-lg-12">Alfa 2</label>	
								<div class="col-lg-12">
									<x-input type="text" name="alfa2" maxlength="2" info="country-alfa2"/>
								</div>
							</div>
						</div>

						{{-- Alfa 3 --}}
						<div class="col-lg-4">
							<div class="row">
								<label for="alfa3" class="col-lg-12">Alfa 3</label>	
								<div class="col-lg-12">
									<x-input type="text" name="alfa2" maxlength="3" info="country-alfa3"/>
								</div>
							</div>
						</div>
					</div>

					<div class="form-group row">
						{{-- Bandera --}}
						<div class="col-lg-4">
							<div class="row">
								<label for="flag" class="col-lg-12">{!! ucfirst(trans('textos.bandera')) !!}</label>	
								<div class="col-lg-12">
									<x-input type="text" name="flag" maxlength="190" info="country-flag"/>
								</div>
							</div>
						</div>
					</div>
					
					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
        </div>
    </div>
</div>

{{-- Edit country --}}
<div class="modal fade modal-edit-country" id="modal-edit-country" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{!! ucfirst(trans('textos.pais_editar')) !!}</h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
				<form method="post" action="{{ route('countries.update') }}" autocomplete="off" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}
					<input type="hidden" name="id" id="countryId">

					<div class="form-group row">
						<div class="col-lg-9">
							<div class="row">
								{{-- País --}}
								<label for="name" class="col-lg-12">{!! ucfirst(trans('textos.pais')) !!} *</label>
								<div class="col-lg-12">
									<x-input type="text" name="name" maxlength="191" id="countryName" required/>
								</div>
							</div>
						</div>

						<div class="col-lg-3">
							<div class="row">
								{{-- Status --}}
								<label for="status" class="col-lg-12">{!! ucfirst(trans('textos.estado')) !!}</label>
								<div class="col-lg-12 text-center">
									<label class="custom-chk-rdb">
						        		<input type="checkbox" name="status" id="countryStatus">
						        		<span class="chk" style="top: 5px;"></span>
						    		</label>
								</div>
							</div>
						</div>
					</div>

					<div class="form-group row">
						{{-- Código --}}
						<div class="col-lg-4">
							<div class="row">
								<label for="code" class="col-lg-12">{!! ucfirst(trans('textos.codigo')) !!}</label>	
								<div class="col-lg-12">
									<x-input type="text" name="code" id="countryCode" maxlength="3" info="country-code"/>
								</div>
							</div>
						</div>

						{{-- Alfa 2 --}}
						<div class="col-lg-4">
							<div class="row">
								<label for="alfa2" class="col-lg-12">Alfa 2</label>	
								<div class="col-lg-12">
									<x-input type="text" name="alfa2" id="countryAlfa2" maxlength="2" info="country-alfa2"/>
								</div>
							</div>
						</div>

						{{-- Alfa 3 --}}
						<div class="col-lg-4">
							<div class="row">
								<label for="alfa3" class="col-lg-12">Alfa 3</label>	
								<div class="col-lg-12">
									<x-input type="text" name="alfa3" id="countryAlfa3" maxlength="3" info="country-alfa3"/>
								</div>
							</div>
						</div>
					</div>

					<div class="form-group row">
						{{-- Bandera --}}
						<div class="col-lg-4">
							<div class="row">
								<label for="flag" class="col-lg-12">{!! ucfirst(trans('textos.bandera')) !!}</label>	
								<div class="col-lg-12">
									<x-input type="text" name="flag" id="countryFlag" maxlength="190" info="country-flag"/>
								</div>
							</div>
						</div>
					</div>
					
					<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function(){
	//Modal editar país:
	$(document).on('click', '.btnEditCountry', function(){
		var id = $(this).data('id');
		var country = $(this).data('country');
		var code = $(this).data('code');
		var alfa2 = $(this).data('alfa2');
		var alfa3 = $(this).data('alfa3');
		var flag = $(this).data('flag');
		var status = $(this).data('status');

		$('#countryId').val(id);
		$('#countryName').val(country);
		$('#countryCode').val(code);
		$('#countryAlfa2').val(alfa2);
		$('#countryAlfa3').val(alfa3);
		$('#countryFlag').val(flag);
		if(status){
			$('#countryStatus').prop('checked', true);
		}
	});
});
</script>
@endpush

@endsection
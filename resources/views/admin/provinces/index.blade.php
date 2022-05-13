@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="countryId" value="{{ $country->id }}">

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		<a href="{{ route('countries.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_paises') !!}
		</a>

		@can('countries.edit')
			<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-new-province"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.provincia_nueva')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12 mb-3">
		<h3>{{ ucfirst(trans('textos.provincias_de')) }} <u>{{ ucfirst($country->name) }}</u></h3>
	</div>

	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblProvinces">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.provincia')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

{{-- Modals --}}
{{-- New province --}}
<div class="modal fade modal-new-province" id="modal-new-province" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{!! ucfirst(trans('textos.provincia_nueva')) !!}</h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
				<form method="post" action="{{ route('provinces.store') }}" autocomplete="off" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}
					<input type="hidden" name="country_id" value="{{ $country->id }}">

					<div class="form-group row">
						<div class="col-lg-9">
							<div class="row">
								{{-- Provincia --}}
								<label for="name" class="col-lg-12">{!! ucfirst(trans('textos.provincia')) !!} *</label>
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
					
					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
        </div>
    </div>
</div>

{{-- Edit province --}}
<div class="modal fade modal-edit-province" id="modal-edit-province" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{!! ucfirst(trans('textos.provincia_editar')) !!}</h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
				<form method="post" action="{{ route('provinces.update') }}" autocomplete="off" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}
					<input type="hidden" name="country_id" value="{{ $country->id }}">
					<input type="hidden" name="id" id="provinceId">

					<div class="form-group row">
						<div class="col-lg-9">
							<div class="row">
								{{-- Provincia --}}
								<label for="name" class="col-lg-12">{!! ucfirst(trans('textos.provincia')) !!} *</label>
								<div class="col-lg-12">
									<x-input type="text" name="name" id="provinceName" maxlength="191" required/>
								</div>
							</div>
						</div>

						<div class="col-lg-3">
							<div class="row">
								{{-- Status --}}
								<label for="status" class="col-lg-12">{!! ucfirst(trans('textos.estado')) !!}</label>
								<div class="col-lg-12 text-center">
									<label class="custom-chk-rdb">
						        		<input type="checkbox" name="status" id="provinceStatus">
						        		<span class="chk" style="top: 5px;"></span>
						    		</label>
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
	//Modal editar provincia:
	$(document).on('click', '.btnEditProvince', function(){
		var id = $(this).data('id');
		var province = $(this).data('province');
		var status = $(this).data('status');

		$('#provinceId').val(id);
		$('#provinceName').val(province);
		if(status){
			$('#provinceStatus').prop('checked', true);
		}
	});
});
</script>
@endpush

@endsection
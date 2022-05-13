@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('companies.create')
			<a class="btn btn-info" href="{{ route('companies.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.empresa_nueva')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblCompanies">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.razon_social')) !!}</th>
					<th>{!! ucfirst(trans('textos.nombre_comercial')) !!}</th>
					<th>{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
					<th>NIF</th>
					<th>Logo</th>
					<th>{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

{{-- Modals --}}
{{-- Transfer company --}}
<div class="modal fade modal-transfer-company" id="modal-transfer-company" tabindex="-1" role="dialog" aria-labelledby="transferCompany" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="transferCompany">{!! ucfirst(trans('textos.empresa_traspaso')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('user-companies.transfer') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="company_id" id="companyId">
                    <input type="hidden" name="validate_transfer_company" value="true">

                    <p class="text-warning">{!! ucfirst(trans('textos.empresa_traspaso_aviso')) !!}</p>

                    <div class="form-group row">
                    	{{-- Nombre destinatario --}}
                    	<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nombre')) !!}*</label>
						<div class="col-lg-10">
							<x-input type="text" name="name" autocomplete="off" maxlength="100" required/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Apellidos destinatario --}}
						<label for="surname" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.apellidos')) !!}*</label>
						<div class="col-lg-10">
							<x-input type="text" name="surname" autocomplete="off" maxlength="191" required/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Email --}}
                    	<label for="email" class="col-lg-2 col-form-label">Email *</label>
						<div class="col-lg-10">
							<x-input type="email" name="email" autocomplete="off" maxlength="191" required/>
						</div>
					</div>

					<p class="text-warning">{!! ucfirst(trans('textos.empresa_traspaso_desvincular')) !!}</p>

					<div class="form-group row">
						{{-- Desvincular usuarios --}}
						<label for="unlink" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.desvincular')) !!}</label>
						<div class="col-lg-2">
							<label class="custom-chk-rdb" style="margin-top: 5px;">
			    				<input type="checkbox" name="unlink">
			    				<span class="chk"></span>
							</label>
						</div>
					</div>	

					<x-btn-submit name="submit_save" class="frm-confirm" datas="msg:{{ trans('textos.empresa_traspaso_confirm') }}" label="{{ trans('textos.enviar') }}"/>
                </form>    
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function(){
	//Modal transferir empresa:
	$(document).on('click', '.btnTransferCompany', function(){
		var id = $(this).data('id');

		$('#companyId').val(id);
	});
});
</script>
@endpush

@endsection
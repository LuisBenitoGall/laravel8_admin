<div class="modal fade opened-modal-edit" id="modal-edit-currency" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{!! ucfirst(trans('textos.moneda_editar')) !!}</h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
				<form method="post" action="" id="frm-edit-currency" autocomplete="off" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}
					<input type="hidden" name="id" id="currencyId">

					<div class="form-group row">
						{{-- Nombre moneda --}}
						<label for="name" class="col-lg-12" required>{!! ucfirst(trans('textos.moneda')) !!} *</label>
						<div class="col-lg-12">
							<x-input type="text" name="name" id="currencyName" maxlength="191" required info="currency-name"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Símbolo --}}
						<label for="symbol" class="col-lg-12">{!! ucfirst(trans('textos.simbolo')) !!}</label>	
						<div class="col-lg-4">
							<x-input type="text" name="symbol" id="currencySymbol" maxlength="2" info="currency-symbol"/>
						</div>
					</div>
					
					<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
        </div>
    </div>
</div>
<div class="modal fade opened-modal-save" id="modal-new-currency" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{!! ucfirst(trans('textos.moneda_nueva')) !!}</h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
				<form method="post" action="{{ route('currencies.store') }}" autocomplete="off" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						<div class="col-lg-9">
							<div class="row">
								{{-- Nombre moneda --}}
								<label for="name" class="col-lg-12">{!! ucfirst(trans('textos.moneda')) !!} *</label>
								<div class="col-lg-12">
									<x-input type="text" name="name" maxlength="191" required info="currency-name"/>
								</div>
							</div>
						</div>

						<div class="col-lg-3">
							<div class="row">
								{{-- Status moneda --}}
								<label for="status" class="col-lg-12">{!! ucfirst(trans('textos.moneda_activa')) !!}</label>
								<div class="col-lg-12 text-center">
									<x-checkbox name="status" checked="true" style="top: 5px;"/>
								</div>
							</div>
						</div>
					</div>

					<div class="form-group row">
						<div class="col-lg-4">
							<div class="row">
								{{-- Código --}}
								<label for="code" class="col-lg-12">{!! ucfirst(trans('textos.codigo')) !!}</label>	
								<div class="col-lg-12">
									<x-input type="text" name="code" maxlength="3" info="currency-code"/>
								</div>
							</div>
						</div>

						<div class="col-lg-4">
							<div class="row">
								{{-- Número ISO --}}
								<label for="number" class="col-lg-12">{!! ucfirst(trans('textos.numero')) !!}</label>	
								<div class="col-lg-12">
									<x-input type="text" name="number" maxlength="4" info="currency-number"/>
								</div>
							</div>
						</div>

						<div class="col-lg-4">
							<div class="row">
								{{-- Símbolo --}}
								<label for="symbol" class="col-lg-12">{!! ucfirst(trans('textos.simbolo')) !!}</label>	
								<div class="col-lg-12">
									<x-input type="text" name="symbol" maxlength="2" info="currency-symbol"/>
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
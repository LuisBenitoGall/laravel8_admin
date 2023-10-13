<div class="modal fade modal-add-serials" id="modal-add-serials" tabindex="-1" role="dialog" aria-labelledby="addSerials" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addSerials">{!! ucfirst(trans('textos.serializar_producto')) !!} <b>{{ $product->name }}</b></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('product-serials.store', $product->id) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}

					<div class="form-group row">
						{{-- Prefijo del serial --}}
                    	<label for="prefix" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.prefijo')) !!}</label>
						<div class="col-lg-9">
							<x-input type="text" name="prefix" autocomplete="off" maxlength="150" required value="{{ $company->acronym }}"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Cantidad --}}
                    	<label for="qty" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.cantidad')) !!}*</label>
						<div class="col-lg-9">
							<x-input type="text" name="qty" autocomplete="off" maxlength="7" required/>
						</div>
					</div>

                    <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                </form>    
            </div>
        </div>
    </div>
</div>
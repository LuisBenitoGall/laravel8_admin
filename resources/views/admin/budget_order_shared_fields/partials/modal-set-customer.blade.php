<div class="modal fade modal-set-customer" id="modal-set-customer" tabindex="-1" role="dialog" aria-labelledby="setCustomer" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="setCustomer">{!! ucfirst(trans('textos.cliente_modificar')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <p class="text-warning">{!! trans('textos.cliente_modificar_aviso') !!}</p>

            	<form method="post" autocomplete="off" action="{{ route('budget-orders.set-customer', $shared) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="environment" value="{{ $environment_ }}">

                    <div class="form-group row">
                        {{-- Empresas --}}
						<label for="new_customer" class="col-lg-12">{!! ucfirst(trans('textos.cliente')) !!} *</label>
                        <div class="col-lg-12">
                            <x-select name="new_customer" class="select2" label="cliente_selec" :options="$customers" required/>
                       </div>
                    </div>

                    <x-btn-submit name="submit_set_customer" label="{{ trans('textos.guardar') }}"/>
                </form> 
            </div>
        </div>
    </div>
</div>

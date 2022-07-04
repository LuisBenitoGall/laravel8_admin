<div class="modal fade modal-edit-payment" id="modal-edit-payment" tabindex="-1" role="dialog" aria-labelledby="editPayment" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editPayment">{!! ucfirst(trans('textos.cobro_editar')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
            	<form method="post" autocomplete="off" action="{{ route('invoice-payments.update') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                    <input type="hidden" name="payment_id" id="paymentId">

                    <div class="form-group row">
                        {{-- Importe --}}
                        <label for="amount" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.importe')) !!}</label>
                        <div class="col-lg-10">
                            <x-input type="text" name="amount" class="text-right set-decimals" id="paymentAmount" autocomplete="off" maxlength="15" value="" group="right" groupvalue="{{ $currency->symbol }}"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Modo --}}
                        <label for="mode" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.modo')) !!}</label>
                        <div class="col-lg-10">
                            <x-select name="mode" class="select2" id="paymentMode" label="metodo_selec" :options="$payment_methods"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Cuenta bancaria --}}
                        <label for="bank_account_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cuenta')) !!}</label>
                        <div class="col-lg-10">
                            <x-select name="bank_account_id" class="select2" id="paymentBankAccount" label="banco_selec" :options="$bank_accounts" value="{{ $bank_featured }}"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Observaciones --}}
                        <label for="observations" class="col-12">{!! ucfirst(trans('textos.observaciones')) !!}</label>
                        <div class="col-12">
                            <x-input-textarea name="observations" id="paymentObservations"/>
                        </div>
                    </div>
                    
                    <x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
                </form>   
            </div>
        </div>
    </div>
</div>
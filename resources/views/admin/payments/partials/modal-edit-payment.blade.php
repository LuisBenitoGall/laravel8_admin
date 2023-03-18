<div class="modal fade modal-edit-payment" id="modal-edit-payment" tabindex="-1" role="dialog" aria-labelledby="editPayment" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editPayment">@if($is_sale) {!! ucfirst(trans('textos.cobro_editar')) !!} @else {!! ucfirst(trans('textos.pago_editar')) !!} @endif</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
            	<form method="post" autocomplete="off" action="{{ route('payments.update') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                    <input type="hidden" name="payment_id" id="paymentId">

                    <div class="form-group row">
                        {{-- Importe --}}
                        <label for="amount" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.importe')) !!}</label>
                        <div class="col-lg-9">
                            <x-input type="text" name="amount" class="text-right set-decimals" id="paymentAmount" autocomplete="off" maxlength="15" value="" group="right" groupvalue="{{ $currency->symbol }}"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Método de pago --}}
                        <label for="payment_method" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.metodo')) !!}</label>
                        <div class="col-lg-9">
                            <x-select name="payment_method" class="select2" id="paymentMethod" label="metodo_selec" :options="$payment_methods"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Cuenta bancaria cliente --}}
                        <label for="bank_account_id" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.cuenta_cliente')) !!}</label>
                        <div class="col-lg-9">
                            <x-select name="bank_account_id" class="select2" id="paymentBankAccount" label="banco_selec" :options="$bank_accounts" value="{{ $bank_featured }}"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Cuenta bancaria proveedor --}}
                        <label for="provider_bank_account_id" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.cuenta_proveedor')) !!}</label>
                        <div class="col-lg-9">
                            <x-select name="provider_bank_account_id" class="select2" label="banco_selec" :options="$provider_bank_accounts" value="{{ $provider_bank_featured }}"/>
                        </div>
                    </div>

                    {{-- Documento --}}
                    <div class="form-group row" id="removeDoc">
                        <label for="remove_doc" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.documento_eliminar')) !!}</label>
                        <div class="col-md-1">
                            <label class="custom-chk-rdb" rel="tooltip">
                                <input type="checkbox" name="remove_doc">
                                <span class="chk" style="top: 5px;"></span>
                            </label>

                            {!! App\Helpers\helpers::info('payment-doc-delete') !!}
                        </div>
                    </div>

                    <div class="form-group row" id="addDoc">
                        <label for="doc" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.documento')) !!}</label>
                        <div class="col-lg-9">
                            <x-input-file name="doc"/>
                            
                            {!! App\Helpers\helpers::info('payment-doc') !!}

                            @if($errors->has('doc'))
                                <span class="invalid-feedback" role="alert">
                                  <strong>{{ $errors->first('doc') }}</strong>
                                </span>
                            @endif    
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
<div class="modal fade modal-add-payment" id="modal-add-payment" tabindex="-1" role="dialog" aria-labelledby="addPayment" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addPayment">@if($is_sale) {!! ucfirst(trans('textos.cobro_add')) !!} @else {!! ucfirst(trans('textos.pago_add')) !!} @endif</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
            	<form method="post" autocomplete="off" action="{{ route('payments.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                    <input type="hidden" name="company_from" value="{{ $customer->id }}">
                    <input type="hidden" name="company_to" value="{{ $provider->id }}">
                    <input type="hidden" name="is_ticket" value="false">

                    <div class="form-group row">
                        {{-- Importe --}}
                        <label for="amount" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.importe')) !!}</label>
                        <div class="col-lg-9">
                            <x-input type="text" name="amount" class="text-right set-decimals" autocomplete="off" maxlength="15" value="" group="right" groupvalue="{{ $currency->symbol }}"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Método de pago --}}
                        <label for="payment_method_id" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.metodo')) !!}</label>
                        <div class="col-lg-9">
                            <x-select name="payment_method_id" class="select2" label="metodo_selec" :options="$payment_methods"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Fecha pago --}}
                        <label for="payment_date" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.pago_fecha')) !!}</label>
                        <div class="col-lg-6">
                            <x-date-picker name="fiscal_date" :format="$languages[config('app.locale')][4]"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Cuenta bancaria cliente --}}
                        <label for="bank_account_id" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.cuenta_cliente')) !!}</label>
                        <div class="col-lg-9">
                            <x-select name="bank_account_id" class="select2" label="banco_selec" :options="$customer_bank_accounts" value="{{ $customer_bank_featured }}"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Cuenta bancaria proveedor --}}
                        <label for="provider_bank_account_id" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.cuenta_proveedor')) !!}</label>
                        <div class="col-lg-9">
                            <x-select name="provider_bank_account_id" class="select2" label="banco_selec" :options="$provider_bank_accounts" value="{{ $provider_bank_featured }}"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Documento --}}
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
                            <x-input-textarea name="observations"/>
                        </div>
                    </div>
                    
                    <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                </form>   
            </div>
        </div>
    </div>
</div>
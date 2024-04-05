<div class="modal fade modal-new-bank-account" id="modal-new-bank-account" tabindex="-1" role="dialog" aria-labelledby="bankAccount" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{!! ucfirst(trans('textos.banco_cuenta_nueva')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('bank-accounts.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="company_id" value="{{ $company_->id }}">
                    <input type="hidden" name="origin" value="modal">
                    <input type="hidden" name="return" value="{{ $business_relationship }}">
                    
                    <div class="form-group row">
                        {{-- Banco --}}
                        <label for="bank_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.banco')) !!}*</label>
                        <div class="col-lg-10">
                            <x-select name="bank_id" class="select2" label="banco_selec" :options="$banks" required/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Cuenta --}}
                        <label for="account" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cuenta')) !!}*</label>
                        <div class="col-lg-10">
                            <x-input type="text" name="account" autocomplete="off" maxlength="25" required/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Cuenta principal --}}
                        <label for="featured" class="col-lg-4 col-form-label">{!! ucfirst(trans('textos.cuenta_principal')) !!}</label>
                        <div class="col-lg-2">
                            <label class="custom-chk-rdb" rel="tooltip">
                                <input type="checkbox" name="featured">
                                <span class="chk" style="top: 5px;"></span>
                            </label>
                        </div>

                        {{-- Status --}}
                        <label for="status" class="col-lg-4 col-form-label">{!! ucfirst(trans('textos.estado')) !!}</label>
                        <div class="col-lg-2">
                            <label class="custom-chk-rdb" rel="tooltip">
                                <input type="checkbox" name="status" checked>
                                <span class="chk" style="top: 5px;"></span>
                            </label>
                        </div>
                    </div>
                    
                    <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                </form>    
            </div>
        </div>
    </div>
</div>
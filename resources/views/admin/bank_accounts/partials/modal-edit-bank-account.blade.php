<div class="modal fade modal-edit-bank-account" id="modal-edit-bank-account" tabindex="-1" role="dialog" aria-labelledby="editBankAccount" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{!! ucfirst(trans('textos.banco_cuenta_editar')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="" id="frmEditBankAccount" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="company_id" value="{{ $company_->id }}">
                    <input type="hidden" name="return" value="{{ $business_relationship }}">
                    <input type="hidden" name="id" id="accountId">
                    
                    <div class="form-group row">
                        {{-- Banco --}}
                        <label for="bank_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.banco')) !!}*</label>
                        <div class="col-lg-10">
                            <x-select name="bank_id" class="select2" id="accountBank" label="banco_selec" :options="$banks" required/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Cuenta --}}
                        <label for="account" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cuenta')) !!}*</label>
                        <div class="col-lg-10">
                            <x-input type="text" name="account" id="accountNumber" autocomplete="off" maxlength="25" required/>
                        </div>
                    </div>
                    
                    <x-btn-submit name="submit_update_" label="{{ trans('textos.guardar') }}"/>
                </form>    
            </div>
        </div>
    </div>
</div>
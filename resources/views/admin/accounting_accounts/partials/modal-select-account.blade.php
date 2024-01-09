<div class="modal fade modal-select-account" id="modal-select-account" tabindex="-1" role="dialog" aria-labelledby="selectAccount" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="selectAccount"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('accounting-accounts.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="mode" value="4">
                    <input type="hidden" name="level1" value="4">  {{-- Tipo cuentas: 4 - Acreedores y deudores por operaciones de tráfico/comerciales --}}
                    <input type="hidden" name="prefix" id="accountPrefix_">
                    <input type="hidden" name="return" value="iva-accounts">
                    <input type="hidden" name="iva_type_id" id="accountIvaType_">
                    <input type="hidden" name="status" value="1">

                    <div class="form-group row">
                        {{-- Cuenta --}}
                        <label for="accounting_account_id_" class="col-12">{!! ucfirst(trans('textos.cuenta')) !!}*</label>
                        <div class="col-12">
                            <x-input type="text" name="accounting_id_" id="search_accounting_account" placeholder="{!! ucfirst(trans('textos.cuenta_buscar')) !!}" maxlength="150"/>

                            <input type="hidden" name="accounting_account_id" id="accountingAccountId">
                        </div>
                    </div>
                    
                    <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>  
                </form>    
            </div>
        </div>
    </div>
</div>
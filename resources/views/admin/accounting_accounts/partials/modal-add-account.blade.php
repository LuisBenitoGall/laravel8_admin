<div class="modal fade modal-add-account" id="modal-add-account" tabindex="-1" role="dialog" aria-labelledby="setColor" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addAccount"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('accounting-accounts.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="mode" value="4">
                    <input type="hidden" name="level1" value="4">  {{-- Tipo cuentas: 4 - Acreedores y deudores por operaciones de tráfico/comerciales --}}
                    <input type="hidden" name="return" value="iva-accounts">

                    <div class="form-group row">
                        {{-- Nombre cuenta --}}
                        <label for="name" class="col-12">{!! ucfirst(trans('textos.cuenta_nombre')) !!}*</label>
                        <div class="col-12">
                            <x-input type="text" name="name" maxlength="255" required/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Activos / Pasivos --}}
                        <label for="type" class="col-12">{!! ucfirst(trans('textos.tipo')) !!}*</label>
                        <div class="col-12">
                            <x-select name="type" class="select2" :options="$tipos" label="tipo_selec" required/>
                        </div>
                    </div>
                    
                    <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>  
                </form>    
            </div>
        </div>
    </div>
</div>
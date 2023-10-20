<div class="modal fade modal-edit-accounting-account-type" id="modal-edit-accounting-account-type" tabindex="-1" role="dialog" aria-labelledby="setCopy" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="setCopy">{!! ucfirst(trans('textos.cuenta_contable_tipo_editar')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('accounting-account-types.update') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="aatId">

                    <div class="form-group row">
                        {{-- Tipo de cuenta --}}
                        <label for="name" class="col-lg-12">{!! ucfirst(trans('textos.cuenta_tipo')) !!}*</label>
                        <div class="col-lg-12">
                            <x-input type="text" name="name" id="aatName" autocomplete="off" maxlength="150"/>    
                       </div>
                    </div>           

                    <x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
                </form>
            </div>
        </div>
    </div>
</div>
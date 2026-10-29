<div class="modal fade modal-add-discount" id="modal-add-discount" tabindex="-1" role="dialog" aria-labelledby="newDiscount" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="newDiscount">{!! ucfirst(trans('textos.descuento')) !!} <b></b></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('customers.add-discount') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="cp_id" value="{{ $conditions->id }}">
                    <input type="hidden" name="customer_id" value="{{ $company->id }}">

                    <div class="form-group row">
                        {{-- Concepto --}}
                        <label for="concept" class="col-lg-12">{!! ucfirst(trans('textos.concepto')) !!}*</label>
                        <div class="col-lg-12">
                            <x-input type="text" name="concept" autocomplete="off" maxlength="150" required/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Descuento --}}
                        <label for="discount" class="col-lg-12">{!! ucfirst(trans('textos.descuento')) !!}*</label>
                        <div class="col-lg-12">
                            <div class="input-group">
                                <x-input type="text" name="discount" class="form-control text-right set-decimals" autocomplete="off" maxlength="5" required/>
                                <div class="input-group-append">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <x-btn-submit name="submit_add_discount" label="{{ trans('textos.guardar') }}"/>
                </form>    
            </div>
        </div>
    </div>
</div>
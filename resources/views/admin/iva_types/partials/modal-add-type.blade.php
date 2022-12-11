<div class="modal fade modal-add-type" id="modal-add-type" tabindex="-1" role="dialog" aria-labelledby="addType" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addType">{!! ucfirst(trans('textos.tipo_nuevo')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
            	<form method="post" autocomplete="off" action="{{ route('iva-types.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}

                    <div class="form-group row">
                    	{{-- Tipo --}}
						<label for="name" class="col-lg-12">{!! ucfirst(trans('textos.nombre')) !!}*</label>
                        <div class="col-lg-12">
                        	<x-input type="text" name="name" autocomplete="off" maxlength="100" required />
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- IVA --}}
                        <div class="col-md-6">
                            <div class="row">
                                <label for="iva" class="col-lg-12">IVA*</label>
                                <div class="col-lg-12">
                                    <x-input type="text" name="iva" class="text-right set-decimals"  autocomplete="off" maxlength="5" group="right" groupvalue="%" required/>    
                                </div>
                            </div>
                        </div>

                        {{-- Recargo de equivalencia --}}
                        <div class="col-md-6">
                            <div class="row">
                                {{-- IVA --}}
                                <label for="equivalence_surcharge" class="col-lg-12">{!! ucfirst(trans('textos.recargo_equivalencia')) !!}</label>
                                <div class="col-lg-12">
                                    <x-input type="text" name="equivalence_surcharge" class="text-right set-decimals"  autocomplete="off" maxlength="5" group="right" groupvalue="%"/>    
                                </div>
                            </div>
                        </div>
                    </div>

                    <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                </form>  
            </div>
        </div>
    </div>
</div>
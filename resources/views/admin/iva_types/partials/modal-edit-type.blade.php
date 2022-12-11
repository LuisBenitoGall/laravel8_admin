<div class="modal fade modal-edit-type" id="modal-edit-type" tabindex="-1" role="dialog" aria-labelledby="editType" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editType">{!! ucfirst(trans('textos.tipo_editar')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
            	<form method="post" autocomplete="off" action="{{ route('iva-types.update') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="typeId">

                    <div class="form-group row">
                    	{{-- Tipo --}}
						<label for="name" class="col-lg-12">{!! ucfirst(trans('textos.nombre')) !!}*</label>
                        <div class="col-lg-12">
                        	<x-input type="text" name="name" id="typeName" autocomplete="off" maxlength="100" required />
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- IVA --}}
                        <div class="col-md-6">
                            <div class="row">
                                <label for="iva" class="col-lg-12">IVA*</label>
                                <div class="col-lg-12">
                                    <x-input type="text" name="iva" id="typeIva" class="text-right set-decimals"  autocomplete="off" maxlength="5" group="right" groupvalue="%" required/>    
                                </div>
                            </div>
                        </div>

                        {{-- Recargo de equivalencia --}}
                        <div class="col-md-6">
                            <div class="row">
                                {{-- IVA --}}
                                <label for="equivalence_surcharge" class="col-lg-12">{!! ucfirst(trans('textos.recargo_equivalencia')) !!}</label>
                                <div class="col-lg-12">
                                    <x-input type="text" name="equivalence_surcharge" id="typeEs" class="text-right set-decimals"  autocomplete="off" maxlength="5" group="right" groupvalue="%"/>    
                                </div>
                            </div>
                        </div>
                    </div>

                    <x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
                </form>  
            </div>
        </div>
    </div>
</div>
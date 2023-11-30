<div class="modal fade modal-edit-block" id="modal-edit-block" tabindex="-1" role="dialog" aria-labelledby="editBlock" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editBlock">{!! ucfirst(trans('textos.bloque_editar')) !!} <span class="userTo"></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('budget-form-items.update') }}" accept-charset="utf-8" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="form_id" value="{{ $form->id }}">
                    <input type="hidden" name="id" id="blockItemsId">

                    {{-- Bloque --}}
					<div class="form-group row">
                        <label for="name" class="col-lg-12">{!! ucfirst(trans('textos.bloque')) !!}*</label>
                        <div class="col-lg-12">
                        	<x-input type="text" name="name" id="blockItemsName" autocomplete="off" maxlength="255" required/>
                        </div>
                    </div>

                    {{-- Centros de coste --}}
                    <div class="form-group row">
                        <label for="cost_center" class="col-lg-12">{!! ucfirst(trans('textos.centro_coste')) !!}</label>
                        <div class="col-lg-12">
                            <x-select name="cost_center" class="select2" id="blockItemsCostCenter" label="centro_coste_selec" :options="$cost_centers"/>
                        </div>
                    </div>
                    
                    <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                </form>    
            </div>
        </div>
    </div>
</div>
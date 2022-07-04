<div class="modal fade modal-add-block" id="modal-add-block" tabindex="-1" role="dialog" aria-labelledby="newBlock" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="newBlock">{!! ucfirst(trans('textos.bloque_nuevo')) !!} <span class="userTo"></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('budget-form-items.store') }}" accept-charset="utf-8" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="form_id" value="{{ $form->id }}">
                    <input type="hidden" name="recid" value="0">

					<div class="form-group row">
                        <label for="name" class="col-lg-12">{!! ucfirst(trans('textos.bloque')) !!}*</label>
                        <div class="col-lg-12">
                        	<x-input type="text" name="name" autocomplete="off" maxlength="255" required/>
                        </div>
                    </div>
                    
                    <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                </form>    
            </div>
        </div>
    </div>
</div>
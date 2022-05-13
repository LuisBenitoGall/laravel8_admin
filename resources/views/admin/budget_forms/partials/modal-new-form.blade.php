<div class="modal fade modal-new-form" id="modal-new-form" tabindex="-1" role="dialog" aria-labelledby="newForm" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="newForm">{!! ucfirst(trans('textos.formulario_nuevo')) !!} <span class="userTo"></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('budget-forms.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    
                    <div class="form-group row">
                    	{{-- Título del formulario --}}
                        <label for="name" class="col-lg-12">{!! ucfirst(trans('textos.formulario_titulo')) !!} *</label>
                        <div class="col-lg-12">
                        	<x-input type="text" name="name" autocomplete="off" maxlength="191" required/>
                        </div>
                    </div>

                    <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                </form>    
            </div>
        </div>
    </div>
</div>
<div class="modal fade modal-add-criterion" id="modal-add-criterion" tabindex="-1" role="dialog" aria-labelledby="addCriterion" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addCriterion">{!! ucfirst(trans('textos.criterio_nuevo')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
            	<form method="post" autocomplete="off" action="{{ route('calendar-criterions.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}

                    <div class="form-group row">
                    	{{-- Criterio --}}
						<label for="name" class="col-lg-12">{!! ucfirst(trans('textos.criterio')) !!}*</label>
                        <div class="col-lg-12">
                        	<x-input type="text" name="name" autocomplete="off" maxlength="100" required />
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Descripción --}}
                        <label for="description" class="col-lg-12">{!! ucfirst(trans('textos.descripcion')) !!}</label>
                        <div class="col-lg-12">
                            <x-input-textarea name="description" class="editor"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Método --}}
                        <label for="method" class="col-lg-12">{!! ucfirst(trans('textos.metodo')) !!}*</label>
                        <div class="col-lg-12">
                            <x-input type="text" name="method" autocomplete="off" maxlength="100" required/>
                        </div>
                    </div>

                    <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                </form>  
            </div>
        </div>
    </div>
</div>
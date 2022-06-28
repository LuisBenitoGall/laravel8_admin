<div class="modal fade modal-add-block" id="modal-add-block" tabindex="-1" role="dialog" aria-labelledby="addBlock" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addBlock">{!! ucfirst(trans('textos.bloque_nuevo')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
            	<form method="post" autocomplete="off" action="{{ route('budget-form-items.add-block') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="budget_id" value="{{ $order->id }}">

                    <div class="form-group row">
                    	{{-- Bloque --}}
						<label for="name" class="col-lg-12">{!! ucfirst(trans('textos.bloque')) !!} *</label>
                        <div class="col-lg-12">
                        	<x-input type="text" name="name" autocomplete="off" maxlength="100" required />
                        </div>
                    </div>

                    <x-btn-submit name="submit-add-block" label="{{ trans('textos.guardar') }}"/>
                </form>  
            </div>
        </div>
    </div>
</div>
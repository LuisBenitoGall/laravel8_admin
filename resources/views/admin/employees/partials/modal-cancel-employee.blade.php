<div class="modal fade opened-modal-save" id="modal-cancel-employee" tabindex="-1" role="dialog" aria-labelledby="cancelEmployee" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="cancelEmployee">{!! ucfirst(trans('textos.contrato_cancelar')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('employees.cancel') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
					<input type="hidden" name="user_id" value="{{ $user->id }}">
					<input type="hidden" name="id" id="employeeIdCancel">

					{{-- Fecha cancelación --}}
					<div class="form-group row">
                        <label for="end" class="col-lg-12">{!! ucfirst(trans('textos.fecha')) !!}</label>
                        <div class="col-lg-12">
                        	<x-date-picker name="end" :format="$languages[config('app.locale')][4]" required/>
                        </div>
                    </div>

                    {{-- Observaciones --}}
                    <div class="form-group row">
                        <label for="observations" class="col-lg-12">{!! ucfirst(trans('textos.observaciones')) !!}</label>
                        <div class="col-lg-12">
                        	<x-input-textarea name="observations"/>
                        </div>
                    </div>

                    <x-btn-submit name="submit_cancel_contract" label="{{ trans('textos.enviar') }}"/>
                </form>    
            </div>
        </div>
    </div>
</div>
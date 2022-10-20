<div class="modal fade opened-modal-edit" id="modal-revision-timetable" tabindex="-1" role="dialog" aria-labelledby="revisionTimetable" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{!! ucfirst(trans('textos.solicitar_revision')) !!} <span class="userTo"></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

			<div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('timetable.update-hour') }}" accept-charset="utf-8" role="form" id="frmRevisionHorario">
                    {{ csrf_field() }}	
                    <input type="hidden" name="id" id="timetableId">
                    <input type="hidden" name="state" id="timetableState">

					<div class="form-group row">
                        {{-- Observaciones --}}
                        <label for="comments" class="col-12">{!! ucfirst(trans('textos.observaciones')) !!} </label>
						<div class="col-lg-12">
                            <x-input-textarea name="comments" id="tmComments"/>
						</div>
                    </div>

                    <x-btn-submit name="submit_save_comments" label="{{ trans('textos.enviar') }}"/>
                </form> 
            </div>   
        </div>
    </div>
</div>
<div class="modal fade modal-edit-participation" id="modal-edit-participation" tabindex="-1" role="dialog" aria-labelledby="editParticipation" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editParticipation">{!! ucfirst(trans('textos.participacion_editar')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('company-utes.update') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="uteId">
                    <input type="hidden" name="ute_id" value="{{ $ute->id }}">

                    <div class="form-group row">
                        {{-- Participación --}}
                        <label for="participation_percent" class="col-12">{!! ucfirst(trans('textos.participacion')) !!}</label>
                        <div class="col-12">
                            <x-input type="text" name="participation_percent" id="uteParticipation" class="text-right set-decimals" autocomplete="off" maxlength="6" group="right" groupvalue="%"/>
                        </div>
                    </div>

                    <x-btn-submit name="submit_update" label="guardar"/>   
                </form>    
            </div>
        </div>
    </div>
</div>
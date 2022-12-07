<div class="modal fade modal-set-criterions" id="modal-set-criterions" tabindex="-1" role="dialog" aria-labelledby="setCriterions" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="setCriterions">{!! ucfirst(trans('textos.calendario_criterios')) !!} <strong></strong></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <p class="text-warning">{!! trans('textos.calendario_criterios_aviso') !!}</p>

                <form method="post" autocomplete="off" action="{{ route('day-types.set-criterions') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    <input type="hidden" name="id" id="setTypeId">
                    {{ csrf_field() }}

                    <div class="form-group row">
                        {{-- Criterios --}}
                        <label for="criterion" class="col-lg-12">{{ ucfirst(trans('textos.criterios')) }}</label>
                        <div class="col-lg-12">
                            <x-select name="criterion" id="setTypeCriterion" class="select2" label="criterio_selec" :options="$criterions"/>
                        </div>
                    </div>

                    <x-btn-submit name="submit_set_criterion" label="{{ trans('textos.guardar') }}"/>
                </form>
            </div>
        </div>
    </div>
</div>
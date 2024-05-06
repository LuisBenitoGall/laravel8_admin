<div class="modal fade modal-edit-functionality" id="modal-edit-functionality" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{!! ucfirst(trans('textos.funcionalidad_editar')) !!}</h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" action="{{ route('functionalities.update') }}" autocomplete="off" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="functionalityId">

                    <div class="form-group row">
                        {{-- Etiqueta de la funcionalidad --}}
                        <label for="label" class="col-lg-12">{!! ucfirst(trans('textos.etiqueta')) !!} *</label>
                        <div class="col-lg-12">
                            <x-input type="text" name="label" id="functionalityLabel" maxlength="191" required/>
                        </div>
                    </div>
                    <br>

                    <div class="form-group row">
                        <div class="col-sm-12 text-right">
                            <input type="submit" name="submit_update" class="btn btn-info" value="{!! ucfirst(trans('textos.guardar')) !!}">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> 
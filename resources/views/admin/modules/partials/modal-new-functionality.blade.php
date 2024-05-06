<div class="modal fade modal-new-functionality" id="modal-new-functionality" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{!! ucfirst(trans('textos.funcionalidad_nueva')) !!}</h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" action="{{ route('functionalities.store') }}" autocomplete="off" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="module_id" value="{{ $module->id }}">

                    <div class="form-group row">
                        {{-- Nombre funcionalidad --}}
                        <label for="functionality" class="col-lg-12">{!! ucfirst(trans('textos.funcionalidad')) !!} *</label>
                        <div class="col-lg-12">
                            <x-input type="text" name="functionality" maxlength="191" required info="functionality-name"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Etiqueta de la funcionalidad --}}
                        <label for="label" class="col-lg-12">{!! ucfirst(trans('textos.etiqueta')) !!} *</label>
                        <div class="col-lg-12">
                            <x-input type="text" name="label" maxlength="191" required info="functionality-label"/>
                        </div>
                    </div>

                    <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                </form>
            </div>
        </div>
    </div>
</div>
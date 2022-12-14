<div class="modal fade modal-edit-config-provider" id="modal-edit-config-provider" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{!! ucfirst(trans('textos.configuracion')) !!} <b></b></h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
            	<div class="row">
            		<p class="col-12 text-warning">{!! trans('textos.establece_por_defecto_texto') !!}</p>
            	</div>

                <form method="post" action="{{ route('providers.config') }}" autocomplete="off" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="provider_id" id="configProvider">

                    {{-- Conceptos contables --}}
                    <div class="form-group row">
                        <label for="ac_id" class="col-lg-12">{!! ucfirst(trans('textos.concepto_general')) !!}</label>
                        <div class="col-lg-12">
                            <x-select name="ac_id" class="select2" id="configAccountingConcept" :options="$concepts" label="conceptos_generales_selec"/>
                        </div>
                    </div>

                    <x-btn-submit name="submit_update_config_provider" label="{{ trans('textos.guardar') }}"/>
                </form>
            </div>
        </div>
    </div>
</div>
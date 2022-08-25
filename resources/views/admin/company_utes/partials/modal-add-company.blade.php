<div class="modal fade modal-add-company" id="modal-add-company" tabindex="-1" role="dialog" aria-labelledby="addCompany" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addCompany">{!! ucfirst(trans('textos.empresas_add')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('company-utes.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="ute_id" value="{{ $ute->id }}">

                    <div class="form-group row">
                        {{-- Empresas --}}
                        <label for="member" class="col-12">{!! ucfirst(trans('textos.empresas')) !!}*</label>
                        <div class="col-12">
                            <x-select name="member" class="select2" :options="$companies" label="empresa_selec"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Participación --}}
                        <label for="participation_percent" class="col-12">{!! ucfirst(trans('textos.participacion')) !!}</label>
                        <div class="col-12">
                            <x-input type="text" name="participation_percent" class="text-right set-decimals" autocomplete="off" maxlength="6" group="right" groupvalue="%"/>
                        </div>
                    </div>

                    <x-btn-submit name="submit_save" label="guardar"/>   
                </form>    
            </div>
        </div>
    </div>
</div>
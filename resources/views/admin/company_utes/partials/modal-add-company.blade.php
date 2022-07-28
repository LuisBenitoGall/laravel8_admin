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
                        <label for="members" class="col-12">{!! ucfirst(trans('textos.empresas')) !!}*</label>
                        <div class="col-12">
                            <x-select name="members[]" class="select2" multiple="true" :options="$companies"/>
                        </div>
                    </div>

                    <x-btn-submit name="submit_save" label="guardar"/>   
                </form>    
            </div>
        </div>
    </div>
</div>
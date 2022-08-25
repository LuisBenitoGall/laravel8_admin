<div class="modal fade modal-link-mother" id="modal-link-mother" tabindex="-1" role="dialog" aria-labelledby="linkMother" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{!! ucfirst(trans('textos.empresa_matriz')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('companies.set-mother') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="company_id" value="{{ $company->id }}">

                    <div class="form-group row">
                        <label for="mother_co" class="col-12">{!! ucfirst(trans('textos.empresa')) !!}*</label>
                        <div class="col-12">
                            <x-select name="mother_co" class="select2" label="empresa_selec" :options="$mother_companies" info="company-mother" required/>   
                        </div>
                    </div>                  
                    
                    <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                </form>    
            </div>
        </div>
    </div>
</div>
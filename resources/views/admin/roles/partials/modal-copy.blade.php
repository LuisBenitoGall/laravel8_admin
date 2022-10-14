<div class="modal fade modal-copy" id="modal-copy" tabindex="-1" role="dialog" aria-labelledby="setCopy" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="setCopy">{!! ucfirst(trans('textos.roles_copiar')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <p class="text-warning">{{ trans('textos.roles_copiar_texto') }}</p>

                <form method="post" autocomplete="off" action="{{ route('roles.copy') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="role_id" value="{{ $role->id }}">

                    <div class="form-group row">
                        {{-- Empresas --}}
                        <label for="customer" class="col-lg-12">{!! ucfirst(trans('textos.empresas')) !!}*</label>
                        <div class="col-lg-12">
                            <x-select name="companies[]" class="select2" :options="$companies" multiple required/>
                       </div>
                    </div>           

                    <x-btn-submit name="submit_copy" label="{{ trans('textos.guardar') }}"/>
                </form>
            </div>
        </div>
    </div>
</div>
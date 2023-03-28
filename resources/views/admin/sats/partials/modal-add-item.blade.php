<div class="modal fade" id="modal-add-item" tabindex="-1" role="dialog" aria-labelledby="addItem" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addItem">{!! ucfirst(trans('textos.informacion_add')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('sat-items.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="sat_id" value="{{ $sat->id }}">

                    <div class="form-group row">
                        {{-- Documento --}}
                        <label for="doc" class="col-12">{!! ucfirst(trans('textos.documento')) !!}</label>
                        <div class="col-12">
                            <x-input-file name="doc"/>

                            @if($errors->has('doc'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('doc') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Información --}}
                        <label for="observations" class="col-12">{!! ucfirst(trans('textos.informacion')) !!}</label>
                        <div class="col-12">
                            <x-input-textarea name="observations" class="editor"/>
                        </div>
                    </div>

                    <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                </form>
            </div>
        </div>
    </div>
</div>
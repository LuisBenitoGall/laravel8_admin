<div class="modal fade" id="modal-edit-note" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    {!! ucfirst(trans('textos.nota_editar')) !!}
                    <small class="ml-5">
                        <a href="{{ route('dashboard') }}">{{ ucfirst(trans('textos.ver_todas')) }}</a>
                    </small>
                </h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('notes.update') }}" id="frmEditNote" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="noteEditId">

                    <div class="form-group row">
                        {{-- Título --}}
                        <div class="col-md-12">
                            <label for="title">{!! trans('textos.titulo') !!} *</label>
                        </div>
                        <div class="col-12">
                            <x-input type="text" name="title" id="noteEditTitle" autocomplete="off" maxlength="150" required/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Nota --}}
                        <div class="col-md-12">
                            <label for="note">{!! trans('textos.nota') !!} *</label>
                        </div>
                        <div class="col-12">
                            <x-input-textarea name="note" id="noteEditContent"/>
                        </div>
                    </div>
                    
                    <x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
                </form>   
            </div>
        </div>
    </div>
</div>
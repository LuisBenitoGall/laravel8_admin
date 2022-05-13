<div wire:ignore.self class="modal fade" id="modal-add-note" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    {!! ucfirst(trans('textos.nota_nueva')) !!}

                    <small class="ml-5">
                        <a href="{{ route('dashboard') }}">{{ ucfirst(trans('textos.ver_todas')) }}</a>
                    </small>
                </h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form autocomplete="off" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    <div class="form-group row">
                        {{-- Título --}}
                        <div class="col-md-12">
                            <label for="title">{!! trans('textos.titulo') !!} *</label>
                        </div>
                        <div class="col-12">
                            <input type="text" class="form-control" wire:model='title'>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Nota --}}
                        <div class="col-md-12">
                            <label for="note">{!! trans('textos.nota') !!} *</label>
                        </div>
                        <div class="col-12">
                            <textarea class="form-control" wire:model="note"></textarea>
                        </div>
                    </div>

                    <div class="row pt-3">
                        <div class="col-sm-12 text-right">
                            <button class="btn btn-info" wire:click.prevent="store()">{{ ucfirst(trans('textos.guardar')) }}</button>
                        </div>
                    </div>
                </form>   
            </div>
        </div>
    </div>
</div>

<script>
window.livewire.on('reponse', () => {
    $('#modal-add-note').modal('hide');
});
</script>



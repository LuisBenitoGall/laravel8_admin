<div class="modal fade modal-add-subtask" id="modal-add-subtask" tabindex="-1" role="dialog" aria-labelledby="addSubtask" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addSubtask">{!! ucfirst(trans('textos.subtarea_nueva')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
				<form method="post" autocomplete="off" action="{{ route('subtasks.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" value="{{ $task->id }}">

                    <div class="form-group row">
                        {{-- Descripción --}}
                        <label for="description" class="col-lg-12">{!! ucfirst(trans('textos.descripcion')) !!}</label>
                        <div class="col-lg-12">
                            <x-input-textarea name="description"/>
                        </div>
                    </div>

					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                </form>
            </div>
        </div>
    </div>
</div>
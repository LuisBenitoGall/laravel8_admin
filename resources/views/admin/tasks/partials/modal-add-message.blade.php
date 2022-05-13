<div class="modal fade modal-new-message" id="modal-new-message" tabindex="-1" role="dialog" aria-labelledby="addMessage" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addMessage">{!! ucfirst(trans('textos.mensaje_nuevo')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
				<form method="post" autocomplete="off" action="{{ route('task-messages.store', $task) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}

					<div class="form-group row">
						{{-- Usuarios --}}
						<label for="to" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.destinatarios')) !!}</label>
						<div class="col-lg-10">
							@php
                            $members_ = [];

                            foreach($team_members as $tm){
                                $members_[$tm->id] = $tm->name.' '.$tm->surname;
                            }
                            @endphp

                            <x-select name="to[]" class="select2" id="selectReceivers" multiple :options="$members_"/>
						</div>
					</div>

					<div class="form-group row mt-4">
						{{-- Mensaje --}}
						<label for="message" class="col-lg-12">{!! ucfirst(trans('textos.mensaje')) !!}</label>
						<div class="col-lg-12">
							<x-input-textarea name="message" class="editor"/>
						</div>
					</div>

					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                </form>
            </div>
        </div>
    </div>
</div>
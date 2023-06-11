<div class="modal fade modal-edit-member" id="modal-edit-member" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="memberGroupTitle">{!! ucfirst(trans('textos.miembro_editar')) !!} <u></u></h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
				<form method="post" action="{{ route('group-members.update') }}" autocomplete="off" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}
					<input type="hidden" name="group_id" value="{{ $group->id }}">
					<input type="hidden" name="id" id="memberGroupId">

					<div class="form-group row">
						{{-- Rol --}}
						<label for="rol" class="col-12">{!! ucfirst(trans('textos.role')) !!}</label>
						<div class="col-12">
							<x-input type="text" name="rol" id="memberGroupRol" autocomplete="off" maxlength="150"/>
						</div>
					</div>
					
					<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
        </div>
    </div>
</div>
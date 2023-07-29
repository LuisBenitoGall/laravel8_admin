<div class="modal fade" id="modal-add-managers" tabindex="-1" role="dialog" aria-labelledby="addManagers" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addManagers">{!! ucfirst(trans('textos.manager_add')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

			<div class="modal-body">
				<form method="post" autocomplete="off" class="eventInsForm" action="{{ route('quizzes.add-managers', $quiz) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
                    {{ csrf_field() }}

                    <div class="form-group row">
                    	{{-- Usuarios --}}
                        <label for="manager_" class="col-lg-12">{!! ucfirst(trans('textos.usuarios')) !!}</label>
                        <div class="col-lg-12">
							<x-input type="text" name="managers" id="search_user" placeholder="{!! ucfirst(trans('textos.usuario')) !!}" maxlength="150"/>

							<input type="hidden" name="manager_" id="userId">
                        </div>
                    </div>

                    <x-btn-submit name="submit_add_managers" label="{{ trans('textos.guardar') }}"/>
                </form>    
			</div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-add-member" tabindex="-1" role="dialog" aria-labelledby="addMember" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addMember">{!! ucfirst(trans('textos.miembro_equipo_add')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

			<div class="modal-body">
				<form method="post" autocomplete="off" action="{{ route('projects.update-team') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" value="{{ $project->id }}">

					@if($project->order_id)
						<div class="form-group row">
							<div class="col-lg-12">
								<label class="custom-chk-rdb" rel="tooltip">
					                <input type="checkbox" name="order_members">
					                <span class="chk"></span>
					            </label>
					            <label for="order_members" class="col-lg-8">{!! ucfirst(trans('textos.pedido_miembros_add')) !!}</label>
							</div>
	                    </div>
	                    <br>
	                @endif

                    <div class="form-group row">
                    	{{-- Usuarios --}}
                        <label for="member" class="col-lg-12">{!! ucfirst(trans('textos.usuarios')) !!}</label>
                        <div class="col-lg-12">
                        	@if($users->count())
	                        	@php
								$users_ = [];
								foreach($users as $user){
									//Sólo usuarios no incluidos en el equipo.
									if(!in_array($user->id, $team)){
										$users_[$user->id] = $user->name.' '.$user->surname;
									}
								}
								@endphp

								<x-select name="member" class="select2" label="usuarios_selec" :options="$users_"/>
							@else
								<p class="text-center text-warning mt-4">{{ trans('textos.usuarios_no_mas') }}</p>
							@endif
                        </div>
                    </div>

                    @if($users->count())
                    	<x-btn-submit name="submit_add_member" label="{{ trans('textos.guardar') }}"/>
                    @endif
                </form>    
			</div>
        </div>
    </div>
</div>
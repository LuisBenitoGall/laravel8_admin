<div class="modal fade modal-new-member" id="modal-new-member" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{!! ucfirst(trans('textos.miembro_nuevo')) !!}</h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
				<form method="post" action="{{ route('group-members.store') }}" autocomplete="off" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}
					<input type="hidden" name="group_id" value="{{ $group->id }}">

					@if($employees)
						<div class="form-group row">
							{{-- Empleados --}}
							<label for="member" class="col-12">{!! ucfirst(trans('textos.miembro')) !!} *</label>
							<div class="col-12">
								<select class="form-control select2" id="miembro" name="member" required>
									<option value="">{!! ucfirst(trans('textos.empleado_selec')) !!}</option>

									@foreach($employees as $employee)
										<option value="{{ $employee->id }}">{{ $employee->name }} {{ $employee->surname }}</option>
									@endforeach
								</select>

				                <span class="invalid-feedback employee_id" role="alert">
				                	<strong></strong>
				                </span>

				                @if(count($employees) == 0)
									<span class="text-danger"><strong>{!! ucfirst(trans('textos.empleados_vacio')) !!}</strong></span>
				                @endif

								@if($errors->has('member'))
	                                <span class="invalid-feedback" role="alert">
	                                  <strong>{{ $errors->first('member') }}</strong>
	                                </span>
	                            @endif
							</div>
						</div>
					@endif

					<div class="form-group row">
						{{-- Rol --}}
						<div class="col-lg-9">
							<div class="row">
								<label for="rol" class="col-12">{!! ucfirst(trans('textos.role')) !!}</label>
								<div class="col-12">
									<x-input type="text" name="rol" autocomplete="off" maxlength="150"/>
								</div>
							</div>
						</div>

						{{-- Responsable --}}
						<div class="col-lg-3">
							<div class="row">
								<label for="responsible" class="col-12">{!! ucfirst(trans('textos.responsable')) !!}</label>
								<div class="col-12 text-center">
									<label class="custom-chk-rdb">
						        		<input type="checkbox" name="responsible">
						        		<span class="chk mt-1"></span>
						    		</label>
								</div>
							</div>
						</div>
					</div>
					
					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
        </div>
    </div>
</div>
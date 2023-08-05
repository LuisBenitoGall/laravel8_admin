<div class="modal fade modal-new-customer-manager" id="modal-new-customer-manager" tabindex="-1" role="dialog" aria-labelledby="newResponsable" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="newResponsable">{!! ucfirst(trans('textos.add_nuevo_responsable')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('budgets.add-manager') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                    <input type="hidden" name="return_view" value="{{ $return_view }}">

                    <div class="form-group row">
                        {{-- Nombre --}}
						<label for="name" class="col-lg-12">{!! ucfirst(trans('textos.nombre')) !!} *</label>
                        <div class="col-lg-12">
                            <x-input type="text" name="name" autocomplete="off" maxlength="100" required/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Apellidos --}}
						<label for="surname" class="col-lg-12">{!! ucfirst(trans('textos.apellidos')) !!} *</label>
                        <div class="col-lg-12">
                            <x-input type="text" name="surname" autocomplete="off" maxlength="100" required/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Email --}}
						<label for="email" class="col-lg-12">Email *</label>
                        <div class="col-lg-12">
                            <x-input type="email" name="email" autocomplete="off" maxlength="150" required/>
                        </div>

                        {{-- Confirmar email ya existente --}}
                        @error('email')
                            <div class="col-lg-12 mt-1">
                                <label class="custom-chk-rdb" rel="tooltip">
                                    <input type="checkbox" name="confirm_email">
                                    <span class="chk chksmall" style="margin-top: 1px;"></span>
                                    <span class="text-warning">{{ trans('textos.email_existe_confirm') }}</span>
                                </label>
                            </div>    
                        @enderror
                    </div>

					<div class="form-group row">
                        {{-- Puesto laboral --}}
                    	<label for="job_title" class="col-lg-12">{!! ucfirst(trans('textos.puesto')) !!}</label>
						<div class="col-lg-12">
                            <x-input type="text" name="job_title" autocomplete="off" maxlength="150"  placeholder="{!! ucfirst(trans('textos.puesto_placeholder')) !!}"/>
						</div>
					</div>
					
                    <x-btn-submit name="submit_add_manager" label="{{ trans('textos.guardar') }}"/>
                </form>    
            </div>
        </div>
    </div>
</div>
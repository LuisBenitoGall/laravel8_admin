<div class="modal fade modal-add-company" id="modal-add-company" tabindex="-1" role="dialog" aria-labelledby="newResponsable" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{!! ucfirst(trans('textos.empresa_nueva')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('companies.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="business_relationship" value="{{ $business_relationship }}">
                    <input type="hidden" name="return" value="{{ $return }}">
                    <input type="hidden" name="return_id" value="{{ $return_id }}">

                    <div class="form-group row">
                        {{-- Razón social de la empresa --}}
                        <label for="name" class="col-12">{!! ucfirst(trans('textos.empresa')) !!}*</label>
                        <div class="col-12">
                            <x-input type="text" name="name" placeholder="{!! ucfirst(trans('textos.empresa')) !!}" autocomplete="off" maxlength="150" required/>
                        </div>
                    </div>

                     <div class="form-group row">
                        {{-- Nombre comercial --}}
                        <label for="tradename" class="col-12">{!! ucfirst(trans('textos.nombre_comercial')) !!}</label>
                        <div class="col-12">
                            <x-input type="text" name="tradename" placeholder="{!! ucfirst(trans('textos.nombre_comercial')) !!}" autocomplete="off" maxlength="150" info="company-tradename"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- NIF --}}
                        <label for="nif" class="col-12">NIF*</label>
                        <div class="col-12">
                            @php
                            $is_invalid = '';
                            @endphp
                            @error('nif')
                                @php
                                $is_invalid = 'is-invalid';
                                @endphp
                            @enderror

                            <x-input type="text" name="nif" class="{{ $is_invalid }}" placeholder="NIF" autocomplete="off" maxlength="30" required info="company-nif"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Nombre --}}
                        <label for="name" class="col-12">{!! ucfirst(trans('textos.nombre_contacto')) !!} *</label>
                        <div class="col-lg-12">
                            <x-input type="text" name="name" autocomplete="off" maxlength="100" required/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Apellidos --}}
                        <label for="surname" class="col-12">{!! ucfirst(trans('textos.apellidos')) !!} *</label>
                        <div class="col-lg-12">
                            <x-input type="text" name="surname" autocomplete="off" maxlength="100" required/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Email --}}
                        <label for="email" class="col-12">Email *</label>
                        <div class="col-lg-12">
                            <x-input type="email" name="email" autocomplete="off" maxlength="150" required/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Puesto laboral --}}
                        <label for="job_title" class="col-lg-12">{!! ucfirst(trans('textos.puesto')) !!}</label>
                        <div class="col-lg-12">
                            <x-input type="text" name="job_title" autocomplete="off" maxlength="150"  placeholder="{!! ucfirst(trans('textos.puesto_placeholder')) !!}"/>
                        </div>
                    </div>

                    <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                </form>    
            </div>
        </div>
    </div>
</div>
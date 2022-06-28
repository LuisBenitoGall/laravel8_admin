<div class="modal fade modal-add-manager" id="modal-add-manager" tabindex="-1" role="dialog" aria-labelledby="newResponsable" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="newResponsable">{!! ucfirst(trans('textos.nuevo_responsable_de')) !!} <b></b></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('customers.add-responsable') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="company_id" id="cpCompanyId">
                    <input type="hidden" name="return" value="{{ $return }}">

                    <div class="form-group row">
                        {{-- Nombre del responsable --}}
                        <label for="name" class="col-lg-12">{!! ucfirst(trans('textos.nombre')) !!} *</label>
                        <div class="col-lg-12">
                            <x-input type="text" name="name" autocomplete="off" maxlength="100" required/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Apellidos --}}
                        <label for="surname" class="col-lg-12">{!! ucfirst(trans('textos.apellidos')) !!} *</label>
                        <div class="col-lg-12">
                            <x-input type="text" name="surname" autocomplete="off" maxlength="150" required/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Email --}}
                        <label for="email" class="col-lg-12">Email *</label>
                        <div class="col-lg-12">
                            <x-input type="email" name="email" autocomplete="off" maxlength="100" required/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Puesto --}}
                        <label for="job_title" class="col-lg-12">{!! ucfirst(trans('textos.puesto')) !!}</label>
                        <div class="col-lg-12">
                            <x-input type="text" name="job_title" autocomplete="off" maxlength="150" info="customer-job-title"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Role --}}
                        <label for="role_id" class="col-lg-12">{!! ucfirst(trans('textos.role')) !!}</label>
                        <div class="col-lg-12">
                            @php
                            $options[] = ''; 
                            @endphp

                            @foreach($basic_roles as $role)    
                                @php
                                $options[$role->id] = $role->name;
                                @endphp 
                            @endforeach

                            <x-select name="role_id" class="select2" :options="$options" info="customer-role"/>
                        </div>
                    </div>
                    
                    <x-btn-submit name="submit-add-responsable" label="{{ trans('textos.guardar') }}"/>
                </form>    
            </div>
        </div>
    </div>
</div>
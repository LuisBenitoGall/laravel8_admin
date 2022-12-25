<form method="post" autocomplete="off" action="{{ route('roles.update', $role->id) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	@method('PUT')
	{{ csrf_field() }}

	<div class="float-fixed-div">
		<div class="row">
			<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}" inline="true"/>
		</div>
	</div>

	<div class="form-group row">
		{{-- Name --}}
		<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.rol_titulo')) !!} *</label>
		<div class="col-lg-4">
			@if($action == 'edit')
				<input type="hidden" name="name" value="{{ $role->name }}">

				@php
				$roleName = config('constants.SUPER_ADMIN_');
				if($role->name != config('constants.SUPER_ADMIN_')){
					$parts = explode('/', $role->name);	
					$roleName = $parts[1];
				}
				@endphp
				<x-input type="text" name="" class="name-slug" maxlength="150" value="{{ ucfirst($roleName) }}" required info="role-name" readonly="true"/>
			@else
				<x-input type="text" name="name" id="name" class="name-slug" maxlength="150" value="" required info="role-name"/>
			@endif
		</div>

		{{-- Switch desplegar opciones --}}
		@if(!$role || $role->name != config('constants.SUPER_ADMIN_'))
			<label for="name" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.permisos_desplegar_todos')) !!}</label>
			<div class="col-lg-2">
				<label class="switch" style="margin-top: 6px;">
		  			<input type="checkbox" class="btnSlideAllPermissions">
		  			<span class="slider round"></span>
				</label>
			</div>
		@endif
	</div>

	<div class="form-group row">
		{{-- Description --}}
		<label for="description" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.descripcion')) !!}</label>
		<div class="col-lg-10">
			@php
			$description = isset($role->description)? $role->description:'';
			@endphp
			<x-input-textarea name="description" value="{{ $description }}"/>
		</div>
	</div>
</form>

{{-- Módulos --}}
@if($role->name != config('constants.SUPER_ADMIN_'))
	<div class="row">
		<div class="col-12 py-3">
			@foreach($modules as $mod_)
				<a href="#module-{{ $mod_->slug }}">
					<span class="label label-info mb-2 d-inline-block">{{ ucfirst(trans('textos.'.$mod_->label)) }}</span>
				</a>
			@endforeach
		</div>
	</div>
@endif

{{-- Loop functionalities --}}
@if($action == 'create' || $role->name != config('constants.SUPER_ADMIN_'))
	<div class="row" id="functionalities">
		{{-- Agrupamos las funcionalidades por módulo --}}
		@foreach($modules as $mod)
			<div class="col-12 mb-2" id="module-{{ $mod->slug }}">
				<div class="widget style1" style="background-color: {{ $mod->color }}; color: #FFF;">
                    <div class="row vertical-align">
                        <div class="col-12">
                        	<h3>{{ strtoupper(trans('textos.'.$mod->label)) }}</h3>
                        </div>
                    </div>
                </div>
			</div>

			@foreach($functionalities as $func)
				@if($func->module_id == $mod->id)
					<div class="col-md-6 col-lg-4">
						<div class="ibox collapsed" data-module="{{ $func->module_id }}">
							<div class="ibox-title bg-muted">
								<h4 class="text-center">
									{{ ucfirst(trans('textos.'.$func->label)) }}
								</h4>

								<div class="ibox-tools">
									<a class="collapse-link" data-slug="{{ $func->slug }}">
		                                <i class="fa fa-chevron-up"></i>
		                            </a>
		                        </div>
		                    </div>

		                    @livewire('permissions-component', ['role' => $role, 'functionality' => $func], key($func->id))
						</div>
					</div>
				@endif
			@endforeach

			<div class="col-12 mb-3"><hr style="margin-top: 0;"></div>

		@endforeach
	</div>
@endif

@push('scripts')
<script>
$(function(){
	// Selección todos permisos:
	$(document).on('change', '.all-permissions', function(){
        var functionality = $(this).data('functionality');
        if($(this).is(':checked')){
            $('.functionality-'+functionality).prop('checked',true);
        }else{
            $('.functionality-'+functionality).prop('checked',false);    
        }
    });	

    // Permisos por funcionalidad:
    $('.collapse-link').on('click', function(){
    	var parent = $(this).closest('.ibox');
    	var slug = $(this).data('slug');
    	$('.ibox').not(parent).find('.ibox-title').removeClass('bg-warning').addClass('bg-muted');

    	if(parent.hasClass('border-bottom')){
    		parent.find('.ibox-title').removeClass('bg-muted').addClass('bg-warning');
    	}else{
    		parent.find('.ibox-title').removeClass('bg-warning').addClass('bg-muted');	
    	}
    });

    // Activar / desactivar permiso:
    $('.chkPermission').on('change', function(){
    	var permission = $(this).val();
    	var role = $('#roleId').val();
    	var action = 'off';

    	if($(this).is(':checked')){
    		action = 'on';
    	}

    	$.get("/admin/roles/"+role+"/set-permission/"+permission+'/'+action);
    });

    // Activar / desactivar todos los permisos de una funcionalidad:
    $('.chkFunc').on('change', function(){
    	var func = $(this).data('functionality');
    	var role = $('#roleId').val();
    	var action = 'off';

    	if($(this).is(':checked')){
    		action = 'on';
    	}

    	$.get("/admin/roles/"+role+"/set-functionality-permissions/"+func+'/'+action);
    });
});
</script>
@endpush
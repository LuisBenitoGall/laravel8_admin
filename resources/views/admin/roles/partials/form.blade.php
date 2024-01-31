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
				if($role && $role->name != config('constants.SUPER_ADMIN_')){
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
		@if($action == 'edit')
			@if(!$role || $role->name != config('constants.SUPER_ADMIN_'))
				<label for="name" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.permisos_desplegar_todos')) !!}</label>
				<div class="col-lg-2">
					<label class="switch" style="margin-top: 6px;">
			  			<input type="checkbox" class="btnSlideAllPermissions">
			  			<span class="slider round"></span>
					</label>
				</div>
			@endif
		@endif
	</div>

	<div class="form-group row">
		{{-- Description --}}
		<label for="description" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.descripcion')) !!}</label>
		<div class="col-lg-10">
			@php
			$description = $role && isset($role->description)? $role->description:'';
			@endphp
			<x-input-textarea name="description" value="{{ $description }}"/>
		</div>
	</div>	
</form>
{{-- / formulario --}}

@if($action == 'edit')
	{{-- Módulos --}}
	@if($role->name != config('constants.SUPER_ADMIN_'))
		<div class="row">
			<div class="col-12 py-3">
				@foreach($modules as $mod_)
					<a href="#module-{{ $mod_->id }}">
						<span class="label label-info mb-2 d-inline-block">{{ ucfirst(trans('textos.'.$mod_->label)) }}</span>
					</a>
				@endforeach
			</div>
		</div>
	@endif

	{{-- Loop functionalities --}}
	@if($action == 'create' || $role->name != config('constants.SUPER_ADMIN_'))
		<div id="functionalities">
			{{-- Agrupamos las funcionalidades por módulo --}}
			@foreach($modules as $mod)
				@php
				$module_on = in_array($mod->id, $module_permissions)? 1:'0';
				@endphp

				<div class="row module_" id="module-{{ $mod->id }}" data-module="{{ $mod->id }}" data-modulename="{{ $mod->slug }}">
					<input type="hidden" id="mod-{{ $mod->id }}" value="{{ $module_on }}">
					
					<div class="col-12 mb-2">
						<div class="widget style1" style="background-color: {{ $mod->color }}; color: #FFF;">
		                    <div class="row vertical-align">
		                        <div class="col-12">
		                        	<h3>
		                        		@if($module_on)
											<i class="fa fa-check"></i>
										@else
											<i class="fa fa-ban"></i>
										@endif

		                        		{{ strtoupper(trans('textos.'.$mod->label)) }}
		                        	</h3>
		                        </div>
		                    </div>
		                </div>
					</div>

					@foreach($functionalities as $func)
						@if($func->module_id == $mod->id)
							<div class="col-md-6 col-lg-4">
								<div class="ibox collapsed">
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
				</div>
			@endforeach
		</div>
	@endif

	@push('scripts')
	<script>
	$(function(){
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
	    	var elem = $(this);
	    	var module_id = elem.closest('.module_').data('module');
	    	var module_on = $('#mod-'+module_id).val() == '0'? module_id:'0';
	    	var permission = elem.val();
	    	var role = $('#roleId').val();
	    	var action = 'off';
	    	var remove_module = '0';

	    	if($(this).is(':checked')){
	    		action = 'on';
	    	}

	    	//Si action == off, comprobamos si quedan permisos activos en el módulo. En caso negativo se cancela también el permiso sobre el módulo:
	    	if(action == 'off'){
	    		remove_module = module_id;
	    		$('#module-'+module_id+' .chkPermission').each(function(){
	    			if($(this).is(':checked')){
	    				remove_module = '0';
	    			}	
	    		});	
	    	}

	    	$.get("/admin/roles/"+role+"/set-permission/"+permission+'/'+action+'/'+module_on+'/'+remove_module, function(response){
	    		if(response){
	    			if(response == 1){
	    				$('#module-'+module_id+' h3 .fa').removeClass('fa-ban').addClass('fa-check');	
	    				$('#mod-'+module_id).val(1);

	    			}else if(response == 2){
	    				$('#module-'+module_id+' h3 .fa').removeClass('fa-check').addClass('fa-ban');	
	    				$('#mod-'+module_id).val('0');
	    			}
	    		}
	    	});
	    });

	    // Activar / desactivar todos los permisos de una funcionalidad:
	    $(document).on('change', '.chkFunc', function(){
	    	let elem = $(this);
	    	let func = elem.data('functionality');
	    	let chkPerm = $('.functionality-'+func);

	        if($(this).is(':checked')){
	            $('.functionality-'+func).prop('checked',true);
	        }else{
	            $('.functionality-'+func).prop('checked',false);    
	        }

	        let arr = [];

		  	// Recorrer los checkbox con la misma data-functionality
		  	chkPerm.each(function() {
		    	arr.push($(this).data('permission'));
		  	});
		  	let permissions = JSON.stringify(arr);
    	
	    	let role = $('#roleId').val();
	    	let module_id = elem.closest('.module_').data('module');
	    	let module_on = $('#mod-'+module_id).val() == '0'? module_id:'0';
	    	let action = 'off';
	    	let remove_module = '0';

	    	if($(this).is(':checked')){
	    		action = 'on';
	    	}

	    	//Si action == off, comprobamos que si quedan permisos activos en el módulo. En caso negativo se cancela también el permiso sobre el módulo:
	    	if(action == 'off'){
	    		remove_module = module_id;
	    		$('#module-'+module_id+' .chkPermission').each(function(){
	    			if($(this).is(':checked')){
	    				remove_module = '0';
	    			}	
	    		});	
	    	}

	    	$.ajax({
	    		url: '/admin/roles/set-functionality-permissions-post',
	    		type: "post",
                dataType: "json",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    permissions: permissions,
                    role_id: role,
                    module_on: module_on,
                    action: action,
                    remove_module: remove_module
                },
                success: function(data){
                	console.log(data);

                }
	    	});

	    	// $.get("/admin/roles/"+role+"/set-functionality-permissions/"+func+'/'+action+'/'+module_on+'/'+remove_module, function(response){
	    	// 	if(response){
	    	// 		if(response == 1){
	    	// 			$('#module-'+module_id+' h3 .fa').removeClass('fa-ban').addClass('fa-check');	
	    	// 			$('#mod-'+module_id).val(1);

	    	// 		}else if(response == 2){
	    	// 			$('#module-'+module_id+' h3 .fa').removeClass('fa-check').addClass('fa-ban');	
	    	// 			$('#mod-'+module_id).val('0');
	    	// 		}
	    	// 	}
	    	// });
	    });
	});
	</script>
	@endpush
@endif


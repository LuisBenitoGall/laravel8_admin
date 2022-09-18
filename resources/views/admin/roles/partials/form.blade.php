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

{{-- Loop functionalities --}}
@if($action == 'create' || $role->name != config('constants.SUPER_ADMIN_'))
	<div class="row">
		@foreach($functionalities as $func)
			<div class="col-md-6 col-lg-4 mb-3">
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

    // Desplegar todos los permisos:
    $('.btnSlideAllPermissions').on('change', function(){
    	if($(this).is(':checked')){
        	$('.ibox').each(function(){
        		$(this).addClass('border-bottom');
        		$(this).find('.ibox-content').slideDown();
        	});
        	
        }else{
        	$('.ibox').each(function(){
        		$(this).removeClass('border-bottom');
        		$(this).find('.ibox-content').slideUp();
        	});
        } 
    });
});
</script>
@endpush
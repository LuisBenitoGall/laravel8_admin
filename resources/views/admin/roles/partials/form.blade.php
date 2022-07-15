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
		@php
		$name = isset($role->name)? $role->name:'';
		$readonly = $action == 'edit' && !$role->company == config('constants.SUPER_ADMIN_')? true:false;
		@endphp
		<x-input type="text" name="name" id="name" class="name-slug" maxlength="150" value="{{ $name }}" required info="role-name" readonly="{{ $readonly }}"/>
	</div>
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
				<div class="permisos">
					<h4 class="text-center mb-3">
						{{ ucfirst(trans('textos.'.$func->label)) }}
					</h4>

					<ul>
						@php
						$allChecked = true;
						$seeAllChecked = false;
						@endphp

						@foreach($permissions as $row)
							{{-- El usuario en sesión debe disponer del permiso --}}
							@can($row->name)
								@if(strpos($row->name, $func->slug.'.') === 0)
									@php
									$checked = (isset($role_permissions) && in_array($row->id, $role_permissions))? 'checked':'';

									$p = explode('.', $row->name);
									$action_ = ucfirst(trans('textos.'.$p[1])).' '.strtolower(trans('textos.'.$func->label));
									@endphp

									<li>
										{{-- Si el permiso es propio del rol, no se puede eliminar --}}
										@if(isset($role_permissions) && in_array($row->id, $role_permissions) && !Auth::user()->hasRole('Super Admin'))
											<i class="fa fa-check text-success mr-3 ml-1"></i>
										@else
											@php
											$seeAllChecked = true;
											@endphp

											<x-checkbox name="permissions[]" inputclass="functionality-{{ $func->slug }}" class="chksmall" value="{{ $row->id }}" checked="{{ $checked }}"/>
										@endif
		                    			{{ $action_ }} <small><b>({{ $row->name }})</b></small>
									</li>

									@php
									if($checked == '') $allChecked = false;
									@endphp
								@endif
							@endcan
						@endforeach	

						<hr>
						<li>
							{{-- Seleccionar todos los permisos: sólo para Super Admin --}}
							@if($seeAllChecked && Auth::user()->hasRole('Super Admin'))
								@php
								$all_checked = $allChecked? 'checked':'';
								@endphp
								<x-checkbox inputclass="all-permissions" class="chksmall" checked="{{ $all_checked }}" datas="functionality.{{ $func->slug }}"/>

		            			<b>{!! trans('textos.todos_permisos_funcionalidad') !!}</b>
		            		@endif
						</li>
					</ul>
				</div>
			</div>
		@endforeach
	</div>
@endif

@push('scripts')
<script>
$(function(){
	$('.all-permissions').on('change', function(){
        var functionality = $(this).data('functionality');
        if($(this).is(':checked')){
            $('.functionality-'+functionality).prop('checked',true);
        }else{
            $('.functionality-'+functionality).prop('checked',false);    
        }
    });	
});
</script>
@endpush
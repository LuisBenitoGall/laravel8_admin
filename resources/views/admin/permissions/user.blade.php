@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		<a href="{{ route('users.edit', $user->id) }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_editar_usuario') !!}
		</a>
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">	
				<h3 class="mb-4"><u>{!! trans('textos.permisos_empleado') !!} <b>{{ $user->name }} {{ $user->surname }}</b></u></h3>

				<form method="post" class="frm-absolute" autocomplete="off" action="{{ route('permissions.update-user-permissions', $user->id) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="float-fixed-div">
						<div class="row">
							<x-btn-submit name="submit_update_permissions" label="{{ trans('textos.guardar') }}" inline="true"/>
						</div>
					</div>

					<div class="row">
						@foreach($functionalities as $func)
							<div class="col-sm-6 col-lg-4 mb-3">
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
											{{-- Si el permiso pertenece a la funcionalidad y el usuario en sesión tiene acceso a este permiso --}}
											@if(strpos($row->name, $func->slug.'.') === 0 && Auth::user()->can($row->name))
												@php
												$checked = (in_array($row->id, $user_permissions))? 'checked':'';

												$p = explode('.', $row->name);
												$action_ = ucfirst(trans('textos.'.$p[1])).' '.strtolower(trans('textos.'.$func->label));
												@endphp

												<li>
													{{-- Si el permiso es propio del rol, no se puede eliminar --}}
													@if(in_array($row->id, $role_permissions_arr))
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
				</form>
			</div>
		</div>
	</div>
</div>

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

@endsection
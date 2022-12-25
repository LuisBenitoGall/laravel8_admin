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

{{-- Módulos --}}
<div class="row">
	<div class="col-11 py-3">
		@foreach($modules as $mod_)
			<a href="#module-{{ $mod_->slug }}">
				<span class="label label-info mb-2 d-inline-block">{{ ucfirst(trans('textos.'.$mod_->label)) }}</span>
			</a>
		@endforeach
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">	
				<div class="form-group row">
					<div class="col-7">
						<h3 class="mb-4"><u>{!! trans('textos.permisos_empleado') !!} <b>{{ $user->name }} {{ $user->surname }}</b></u></h3>	
					</div>

					{{-- Desplegar permisos --}}
					<label for="name" class="col-sm-4 col-md-3 col-form-label">{!! ucfirst(trans('textos.permisos_desplegar_todos')) !!}</label>
					<div class="col-sm-2 col-md-1">
						<label class="switch" style="margin-top: 6px;">
				  			<input type="checkbox" class="btnSlideAllPermissions">
				  			<span class="slider round"></span>
						</label>
					</div>
				</div>	

				<form method="post" class="frm-absolute" autocomplete="off" action="{{ route('permissions.update-user-permissions', $user->id) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="float-fixed-div">
						<div class="row">
							<x-btn-submit name="submit_update_permissions" label="{{ trans('textos.guardar') }}" inline="true"/>
						</div>
					</div>

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
									<div class="col-sm-6 col-lg-4">
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

						                    <div class="ibox-content">
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

															<li class="mb-2">
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
									</div>
								@endif
							@endforeach

							<div class="col-12 mb-3"><hr style="margin-top: 0;"></div>

						@endforeach
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

{!! App\Helpers\helpers::scrollToTop() !!}

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
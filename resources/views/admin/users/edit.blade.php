@extends('admin.partials.layout')

@section('content')

@if(isset($user))
	<div class="row mb-3">
		{{-- Acciones --}}
		<div class="col-12">
			{{-- Listado usuarios --}}
			@can('users.index')
				<a href="{{ route('users.index') }}" class="btn btn-info">
					<i class="fa fa-angle-left"></i>
					{!! trans('textos.volver_usuarios') !!}
				</a>
			@endcan

			{{-- Permisos --}}
			@can('permissions.edit')
				{{-- Los Super Admin no editan sus permisos, por defecto tienen acceso a todo --}}
				@if($user->id != Auth::user()->id || !$user->hasRole(config('constants.SUPER_ADMIN_')))
					<a href="{{ route('permissions.user', $user->id) }}" class="btn btn-info">
						<i class="fa fa-user-lock"></i>
						{!! ucfirst(trans('textos.permisos')) !!}
					</a>
				@endif
			@endcan

			{{-- Contratos --}}
			@if($module_rrhh)
				@can('employees.edit')
					<a href="{{ route('employees.user', $user) }}" class="btn btn-info">
						<i class="fa fa-wrench"></i>
						{!! trans('textos.datos_profesionales') !!}
					</a>
				@endcan
			@endif

			{{-- Empresas --}}
			@if(count(session('companies')) > 1)
				@can('companies.edit')
					<a href="{{ route('user-companies.index', $user->id) }}" class="btn btn-info">
						<i class="fa fa-building"></i>
						{!! ucfirst(trans('textos.empresa_vincular')) !!}
					</a>
				@endcan
			@endif

			{{-- Horarios. Condiciones:
				1. Estar activo el módulo de horario.
				2. Estar el usuario vinculado al menos a 1 centro de trabajo.
			--}}
			@if($module_timetables && $user_workplaces && $user_workplaces->count())
				<a href="{{ route('users.timetables', $user) }}" class="btn btn-info">
					<i class="fa fa-clock"></i>
					{!! ucfirst(trans('textos.horarios')) !!}
				</a>	
			@endif

			{{-- Calendario usuario. Condiciones:
				1. Estar activo el módulo de calendario.
				2. Estar el usuario vinculado al menos a 1 centro de trabajo.
			 --}}
			@if($module_calendar && $user_workplaces && $user_workplaces->count())
				<a href="{{ route('work-user-calendar.index', $user->id) }}" class="btn btn-info">
					<i class="fa fa-calendar"></i>
					{!! ucfirst(trans('textos.calendario_laboral')) !!}
				</a>	
			@endif
		</div>
	</div>

	<div class="row mb-3">
		<div class="col-12">						
			<div class="card mb-3">
				<div class="card-body">
					<h3 class="mb-4"><u>{{ ucwords($user->name) }} {{ ucwords($user->surname) }}</u></h3>	

					{{-- Tabs: --}}
					<ul class="nav nav-tabs" role="tablist">
				  		<li class="nav-item @if(!$tab) active @endif">
				    		<a class="nav-link @if(!$tab) active @endif" href="#tab-datos-personales" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.datos_personales')) !!}</a>
				  		</li>

				  		@if($user->id == Auth::user()->id)
					  		<li class="nav-item @if($tab == 'password') active @endif">
					    		<a class="nav-link @if($tab == 'password') active @endif" href="#tab-password" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.password')) !!}</a>
					  		</li>
				  		@endif

				  		<li class="nav-item @if($tab == 'images') active @endif">
				    		<a class="nav-link @if($tab == 'images') active @endif" href="#tab-images" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.imagenes')) !!}</a>
				  		</li>

				  		<li class="nav-item @if($tab == 'contact') active @endif">
				    		<a class="nav-link @if($tab == 'contact') active @endif" href="#tab-contact" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.contacto')) !!}</a>
				  		</li>

						@if($employee)
							<li class="nav-item @if($tab == 'company') active @endif">
					    		<a class="nav-link @if($tab == 'company') active @endif" href="#tab-company" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.empresa')) !!}</a>
					  		</li>
					  	@endif

					  	@if($protocols->count())
					  		@php
					  		//Protocolos pendientes de firma:
					  		$without_sign = 0;
					  		foreach($protocols as $p){
					  			if(!$p->signed_at){
					  				$without_sign++;
					  			}
					  		}
					  		@endphp
							<li class="nav-item @if($tab == 'protocols') active @endif">
					    		<a class="nav-link @if($tab == 'protocols') active @endif" href="#tab-protocols" role="tab" data-toggle="tab">
					    			{!! ucfirst(trans('textos.protocolos')) !!}
					    			@if($without_sign > 0)
					    				<span class="ml-2 badge badge-danger">{{ $without_sign }}</span>	
					    			@endif
					    		</a>
					  		</li>
					  	@endif

				  		{{-- <li class="nav-item @if($tab == 'notificaciones') active @endif">
				    		<a class="nav-link @if($tab == 'notificaciones') active @endif" href="#tab-notificaciones" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.notificaciones')) !!} @if($nNotifs > 0) <span class="tip">{{ $nNotifs }}</span> @endif</a>
				  		</li> 
				  		<li class="nav-item @if($tab == 'profesional') active @endif">
				    		<a class="nav-link @if($tab == 'profesional') active @endif" href="#tab-datos-profesionales" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.datos_profesionales')) !!}</a>
				  		</li>

				  		@can('roles.edit')
					  		<li class="nav-item @if($tab == 'permisos') active @endif">
					    		<a class="nav-link @if($tab == 'permisos') active @endif" href="#tab-permisos" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.permisos')) !!}</a>
					  		</li>
					  	@endcan

				  		<li class="nav-item @if($tab == 'horarios_firmados') active @endif">
				    		<a class="nav-link @if($tab == 'horarios_firmados') active @endif" href="#tab-horarios-firmados" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.horarios_firmados')) !!}</a>
				  		</li> --}}

						{{-- @if($stocks->count() > 0)
							<li class="nav-item @if($tab == 'stocks') active @endif">
					    		<a class="nav-link @if($tab == 'stocks') active @endif" href="#tab-stocks" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.almacen_equipo_asignado')) !!}</a>
					  		</li>
					  	@endif --}}
					</ul>

					<div class="tab-content pt-5">
						{{-- Datos personales: --}}
						<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-datos-personales">
							@include('admin.users.partials.user-info', ['user' => $user])	
						</div>	

						{{-- Password: --}}
						@if($user->id == Auth::user()->id)
							<div class="tab-pane fade @if($tab == 'password') show active @endif" role="tabpanel" id="tab-password">
								@include('admin.users.partials.user-password', ['user' => $user])	
							</div>
						@endif

						{{-- Imágenes: --}}
						<div class="tab-pane fade @if($tab == 'images') show active @endif" role="tabpanel" id="tab-images">
							@include('admin.users.partials.user-images', ['user' => $user])	
						</div>

						{{-- Contacto: --}}
						<div class="tab-pane fade @if($tab == 'contact') show active @endif" role="tabpanel" id="tab-contact">
							@include('admin.users.partials.user-contact', ['user' => $user])	
						</div>

						{{-- Empresa: --}}
						@if($employee)
							<div class="tab-pane fade @if($tab == 'company') show active @endif" role="tabpanel" id="tab-company">
								@include('admin.users.partials.user-company')	
							</div>
						@endif

						{{-- Equipo asignado --}}
						{{-- @if($stocks->count() > 0)
							<div class="tab-pane fade @if($tab == 'stocks') show active @endif" role="tabpanel" id="tab-stocks">
								@include('admin.users.partials.stocks')	
							</div>
						@endif --}}

						{{-- Protocolos --}}
						@if($protocols->count())
							<div class="tab-pane fade @if($tab == 'protocols') show active @endif" role="tabpanel" id="tab-protocols">
								@include('admin.users.partials.user-protocols')	
							</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>

	{{-- Modals --}}
	{{-- Devolución equipo --}}
	{{-- <div class="modal fade modal-notificate-devolution" id="modal-notificate-devolution" tabindex="-1" role="dialog" aria-labelledby="notificateDevolution" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="notificateDevolution">{{ trans('textos.devolucion_producto') }} <u></u></h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-body">
					<form method="post" autocomplete="off" action="{{ route('stock-returns.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
						{{ csrf_field() }}	
						<input type="hidden" name="user" value="{{ $user->id }}">
						<input type="hidden" name="stock_id" id="returnStockId">
						<input type="hidden" name="qty" id="returnQty">

						<div class="form-group row">
							<label class="col-12">{{ trans('textos.notificar_a') }} *
								</label>
							<div class="col-12">
								<select name="users[]" class="form-control select2" multiple required>
									@foreach($employees as $employee)
										@if($employee->id != $user->id)
											<option value="{{ $employee->id }}">{{ $employee->name }} {{ $employee->surname }}</option>
										@endif
									@endforeach
								</select>

								@if($errors->has('users'))
                                	<span class="invalid-feedback" role="alert">
                                  		<strong>{{ $errors->first('users') }}</strong>
                                	</span>
                            	@endif
							</div>
						</div>

						<div class="form-group row">
							<label class="col-12">{{ trans('textos.motivos') }}
								</label>

							<div class="col-12">
								<textarea name="observations" class="form-control"></textarea>
							</div>
						</div>

						{!! App\Helpers\Htmlelements::submit('submit_store_return', 'enviar') !!}
					</form>
				</div>
			</div>
		</div>
	</div> --}}

	@push('scripts')
	<script>
	$(function(){
		// Aceptación con contraseña:
	    $(document).on('submit', '.frmAcceptWithPass', function(e){
	        e.preventDefault();
	        var data = $(this).serialize();
	        var elem = $(this);

	        $.ajax({
	            url: "/admin/protocol-users/sign",
	            type: "POST",
	            data: data, 
	            cache: false,
	            datatype: "JSON",
	            success: function(response){
	                if(response){
	                    var father = elem.parent();
	                    father.addClass('text-right').html(response);

	                }else{
	                    elem.find('.hold-pwd').append('<small class="text-danger">'+txtPasswordIncorrecto+'</small>');
	                }
	            }
	        });
	    })
	});
	</script>
	@endpush
@endif

@endsection
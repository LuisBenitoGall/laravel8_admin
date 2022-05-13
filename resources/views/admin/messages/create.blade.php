@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-sm-12">		
		<div class="card mb-3">
			<div class="card-body">				
				<form method="post" autocomplete="off" action="{{ route('messages.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Título --}}
						<label for="title" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.titulo')) !!}</label>
						<div class="col-lg-4">
							<x-input type="text" name="title" autocomplete="off" maxlength="150"/>
						</div>
						
						@if(count(session('companies')) > 0)
							{{-- Empresas --}}
							<label for="companies" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.empresas')) !!}</label>
							<div class="col-md-2 col-lg-3">
								@php
								$companies_ = [];
								@endphp

								@foreach(session('companies') as $company)
									@php
									$companies_[$company->id] = $company->name;
									@endphp
								@endforeach

								<x-select name="companies[]" class="select2" multiple :options="$companies_"/>
							</div>

							{{-- Todas las empresas --}}
							<div class="col-md-2 col-lg-1">
								<label class="col-form-label">{{ trans('textos.todas') }}</label>	
								<label class="custom-chk-rdb" rel="tooltip">
				                	<input type="checkbox" name="companies_all">
				                	<span class="chk" style="margin-left: 5px;"></span>
				            	</label>
							</div>
						@endif
					</div>

					<div class="form-group row">
						{{-- Usuarios --}}
						<label for="users" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.usuarios')) !!}</label>
						<div class="col-md-2 col-lg-3">
							@php
							$users_ = [];
							@endphp

							@foreach($users as $user)
								@php
								$users_[$user->id] = $user->name;
								@endphp
							@endforeach

							<x-select name="users[]" class="select2" multiple :options="$users_"/>
						</div>

						{{-- Todos los usuarios --}}
						<div class="col-md-2 col-lg-1">
							<label class="col-form-label">{{ trans('textos.todos') }}</label>	
							<label class="custom-chk-rdb" rel="tooltip">
			                	<input type="checkbox" name="users_all">
			                	<span class="chk" style="margin-left: 5px;"></span>
			            	</label>
						</div>

						{{-- Grupos --}}
						<label for="groups" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.grupos')) !!}</label>
						<div class="col-md-2 col-lg-3">
							@php
							$groups_ = [];
							@endphp

							@foreach($groups as $group)
								@php
								$groups_[$group->id] = $group->name.'('.$group->Company.')';
								@endphp
							@endforeach

							<x-select name="groups[]" class="select2" multiple :options="$groups_"/>
						</div>

						{{-- Todos los grupos --}}
						<div class="col-md-2 col-lg-1">
							<label class="col-form-label">{{ trans('textos.todos') }}</label>	
							<label class="custom-chk-rdb" rel="tooltip">
			                	<input type="checkbox" name="groups_all">
			                	<span class="chk" style="margin-left: 5px;"></span>
			            	</label>
						</div>
					</div>

					{{-- Contenido --}}
					<div class="form-group row">
						<label for="content" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.mensaje')) !!}*</label>
						<div class="col-lg-10">
							<x-input-textarea name="content" class="editor"/>
						</div>
					</div>
					
					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection
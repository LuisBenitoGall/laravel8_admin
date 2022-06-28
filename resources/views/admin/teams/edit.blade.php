@extends('admin.partials.layout')

@section('content')

@php
$languages = App\Helpers\Helpers::languages();
@endphp

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		<a href="{{ route('teams.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_equipos') !!}
		</a>

		@can('teams.create')
    		<a href="{{ route('teams.create') }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.equipo_nuevo')) !!}
    		</a>
        @endcan

        <a href="{{ route('team-members.create', $team) }}" class="btn btn-info">
			<i class="fa fa-plus"></i>
			{!! ucfirst(trans('textos.miembro_nuevo')) !!}
		</a>
	</div>
</div>
		
<div class="row mb-3">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<h3 class="mb-4">
					{!! ucfirst(trans('textos.equipo_editar')) !!} 
					<u>{{ $team->name }}</u>
				</h3>	

				<form method="post" autocomplete="off" action="{{ route('teams.update', $team) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Nombre equipo --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nombre')) !!}*</label>
						<div class="col-lg-6">
							<x-input type="text" name="name" id="name" value="{!! $team->name !!}" autocomplete="off" maxlength="75" required/>
						</div>
						
						{{-- Status --}}
						<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.activo')) !!}</label>
						<div class="col-lg-1">
							<label class="custom-chk-rdb" rel="tooltip">
				                <input type="checkbox" name="status" @if($team->status == 1) checked @endif>
				                <span class="chk" style="top: 5px;"></span>
				            </label>

				            <span class="relativo ml-2">
				            	{!! App\Helpers\Helpers::info('user-activo') !!}
				            </span>
						</div>
					</div>

					<div class="form-group row">
                        {{-- Logo --}}
                        @if($team->logo)
                        	<label class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.logo')) !!}</label>

							<div class="col-lg-4 foto">
								<img class="img-fluid" src="{{ asset('img/teams/'.$team->logo) }}">	

								<div class="controls text-right pt-2">
									<a href="{{ route('teams.delete-logo', $team) }}" class="btn-confirm" data-msg="{{ ucfirst(trans('textos.imagen_confirm_eliminar')) }}" rel="tooltip" title="{{ ucfirst(trans('textos.imagen_eliminar')) }}">
										<i class="fa fa-trash"></i>
									</a>
								</div>
							</div>

						@else
	                        <label for="logo" class="col-md-2 col-form-label">Logo
								<br>
								<small class="text-warning">
									· {{ trans('textos.formato') }}: .jpg, .png, .gif <br>
									· {{ trans('textos.tamany_max') }}: 1MB	<br>
									· {{ trans('textos.medidas_recomendadas') }}: 400x400px							
								</small>
							</label>
							<div class="col-md-4">
								<x-input-file name="logo"/>

								@if($errors->has('logo'))
				                    <span class="invalid-feedback" role="alert">
				                      <strong>{{ $errors->first('logo') }}</strong>
				                    </span>
				                @endif
							</div>
						@endif
					</div>
					
					<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>	
	</div>
</div>

@if($members->count())
	<div class="row">
		<div class="col-12 mb-3">
			<h3>{{ ucfirst(trans('textos.miembros')) }} <u></u></h3>
		</div>

		<div class="table-responsive">
			<table class="table table-hover dTable" id="tblTeamMembers">
				<thead>
					<tr>
						<th>{{ ucfirst(trans('textos.miembro')) }}</th>
						<th>{{ ucfirst(trans('textos.nivel')) }}</th>
						<th class="text-center">{{ ucfirst(trans('textos.fecha_alta')) }}</th>
						<th class="text-center">{{ ucfirst(trans('textos.acciones')) }}</th>
					</tr>
				</thead>

				<tbody>
					@foreach($members as $member)
						<tr>
							<td>{{ ucfirst($member->name) }} {{ ucfirst($member->surname) }}</td>
							
							<td>
								@if($member->leader == 1)
									{{ ucfirst(trans('textos.lider')) }}
								@else
									{{ ucfirst(trans('textos.miembro')) }}
								@endif
							</td>

							<td class="text-right">
								@php
								$alta = $member->created_at->format($languages[session()->get('locale')][4].' H:i:s');
								@endphp
								{!! $alta !!}
							</td>

							{{-- Acciones --}}
							<td class="text-right">
								{{-- Líder --}}
								@if($member->leader == 1)
                        			<i class="fa fa-star text-warning ml-1 mr-2" rel="tooltip" title="{{ ucfirst(trans('textos.lider')) }}"></i>
                        		@else
			                        <a href="{{ route('team-members.set-leader', $member) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.equipo_marcar_lider')) }}"><i class="fa fa-star"></i></a>
			                    @endif   

								{{-- Editar --}}
								<a href="{{ route('team-members.edit', $member) }}" class="btn btn-xs btn-info btn-module" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>

								{{-- Eliminar --}}
								<form method="POST" action="{{ route('team-members.destroy', $member) }}" class="frm-inline" role="form" accept-charset="UTF-8">
                            		<input name="_method" type="hidden" value="DELETE">
                            		{{ csrf_field() }}
                            		<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.miembro_eliminar_confirm')) }}"><i class="fa fa-trash"></i></button>
                        		</form>
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
@endif

@endsection
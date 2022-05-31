@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Listado de procesos --}}
		@can('staff-pick.edit')
			<a href="{{ route('staff-pick.edit', $process) }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_proceso') !!}
			</a>
		@endcan

		{{-- Nuevo candidato --}}
		@can('staff-pick.edit')
			<a href="{{ route('staff-pick-candidates.create', $process) }}" class="btn btn-info">
				<i class="fa fa-plus"></i>
				{!! trans('textos.candidato_nuevo') !!}
			</a>
		@endcan

		{{-- Nuevo comentario --}}
		@can('staff-pick.edit')
			<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-add-observation">
				<i class="fa fa-plus"></i>
				{!! trans('textos.comentario_nuevo') !!}
			</a>
		@endcan

		{{-- Incorporar a empresa --}}
		@can('staff-pick.edit')
			<a href="{{ route('users.from-candidate', $candidate->user_id) }}" class="btn btn-info btn-confirm" data-msg="{{ trans('textos.usuario_desde_candidato_confirm') }}">
				<i class="fa fa-user-plus"></i>
				{!! trans('textos.incorporar_empresa') !!}
			</a>	
		@endcan
	</div>
</div>

<div class="row mb-3">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-3">
					{!! ucfirst(trans('textos.candidato')) !!} 
					<u>
						{{ $user->name }} {{ $user->surname }}
					</u>
				</h3>

				{{-- Tabs: --}}
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item @if(!$tab) active @endif">
			    		<a class="nav-link @if(!$tab) active @endif" href="#tab-info" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.informacion')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'observations') active @endif">
			  			<a class="nav-link @if($tab == 'observations') active @endif" href="#tab-observations" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.comentarios')) !!}</a>	
			  		</li>
			  	</ul>

			  	<div class="tab-content pt-4">
			  		{{-- Info --}}
			  		<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-info">
						@include('admin.staff_pick_candidates.partials.candidate-info')	
					</div>

					{{-- Observaciones --}}
					<div class="tab-pane fade @if($tab == 'observations') show active @endif" role="tabpanel" id="tab-observations">
						@include('admin.staff_pick_candidates.partials.candidate-observations')
					</div>
			  	</div>
			</div>
		</div>	
	</div>
</div>

{{-- Modals --}}
{{-- Add observation --}}
@include('admin.staff_pick_candidates.partials.modal-add-observation')

@endsection
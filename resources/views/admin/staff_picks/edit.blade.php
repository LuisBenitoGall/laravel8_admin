@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		@can('staff-pick.index')
			<a href="{{ route('staff-pick.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_seleccion_procesos') !!}
			</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-3">
					{!! ucfirst(trans('textos.proceso')) !!} 
					<u>
						@if($process->name)
							{{ $process->name }}
						@else
							{{ $process->id }}
						@endif
					</u>
				</h3>

				{{-- Tabs: --}}
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item @if(!$tab) active @endif">
			    		<a class="nav-link @if(!$tab) active @endif" href="#tab-info" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.informacion')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'candidates') active @endif">
			  			<a class="nav-link @if($tab == 'candidates') active @endif" href="#tab-candidates" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.candidatos')) !!}</a>	
			  		</li>
			  	</ul>

			  	<div class="tab-content pt-4">
			  		{{-- Info --}}
			  		<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-info">
						@include('admin.staff_picks.partials.staff-pick-info')	
					</div>

					{{-- Candidatos --}}
					<div class="tab-pane fade @if($tab == 'candidates') show active @endif" role="tabpanel" id="tab-candidates">
						@include('admin.staff_picks.partials.staff-pick-candidates')
					</div>
			  	</div>
			</div>
		</div>
	</div>
</div>

@endsection


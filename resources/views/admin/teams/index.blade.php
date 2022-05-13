@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('teams.create')
    		<a href="{{ route('teams.create') }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.equipo_nuevo')) !!}
    		</a>
        @endcan

        {{-- Toggle vista - cuadrícula --}}
        @include('admin.partials.toggle-views', ['cookie' => 'teams'])
	</div>
</div>

{{-- Vista listado --}}
<div class="row">
	<div class="table-responsive toggle-view-list">
		<table class="table table-hover dtabla" id="tblTeams">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.equipo')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
					<th>{!! ucfirst(trans('textos.logo')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

{{-- Vista cuadrícula --}}
<div class="row">
	<div class="toggle-view-grid">
		<div class="row">
			@foreach($teams as $row)
				<div class="col-sm-12 col-md-6 col-lg-3 mb-3">
					<div class="team">
						<h5>
							<a href="{{ route('teams.edit', $row->id) }}">{{ $row->name }}</a>
						</h5>

						<div class="team-image">
							@if($row->doc)
								<img src="{!! asset('img/teams/'.$row->doc) !!}" class="img-fluid" alt="{{ $row->name }}">
							@else
								<div class="team-image-default">
									<i class="fa fa-gift"></i>
								</div>
							@endif	
						</div>	

						<div class="team-data">
							@if($row->price)
								{{ ucfirst(trans('textos.precio')) }}: <b>{{ $row->price }}{{ $currency}}</b>
							@endif

							<a href="{{ route('teams.edit', $row->id) }}" class="pull-right">
								<i class="fa fa-edit" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"></i>
							</a>
							<div class="clearfix"></div>
						</div>

						@if($row->description)
							@php
							$description = unserialize($row->description);
							@endphp

							<div class="team-text">
								{!! $description[session()->get('locale')] !!}	
							</div>
						@endif
					</div>	
				</div>
			@endforeach
		</div>
	</div>
</div>

@endsection
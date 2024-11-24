@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Volver a incidencias --}}
		@can('incidences.index')
			<a href="{{ route('incidences.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_incidencias') !!}
			</a>
		@endcan

		{{-- Abrir nueva incidencia --}}
		<a href="{{ route('incidences.create') }}" class="btn btn-info">
			<i class="fa fa-plus"></i>
			{!! ucfirst(trans('textos.incidencia_nueva')) !!}
		</a>
	</div>
</div>

<div class="row mb-3">
	<div class="col-12">						
		<div class="card">
			<div class="card-body">
				<h3 class="mb-3">{{ ucfirst(trans('textos.incidencia')) }} <u>{{ ucfirst($incidence->concept) }}</u></h3>

				{{-- Info --}}
				<div class="mb-3">
					{{-- Autor --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.autor')) !!}: <b>{{ $incidence->author->name }} {{ $incidence->author->surname }}</b>
					</label>

					{{-- Fecha alta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.fecha_alta')) !!}: 
						<b>
							{{ $incidence->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Fecha última actualización --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.ultima_edicion')) !!}: 
						<b>
							{{ $incidence->updated_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>
				</div>

				{{-- Tabs: --}}
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item @if(!$tab) active @endif">
			    		<a class="nav-link @if(!$tab) active @endif" href="#tab-incidence-info" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.incidencia')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'reel') active @endif">
				    	<a class="nav-link @if($tab == 'reel') active @endif" href="#tab-incidence-reel" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.hilo')) !!}</a>
				  	</li>

				  	<li class="nav-item @if($tab == 'docs') active @endif">
				    	<a class="nav-link @if($tab == 'docs') active @endif" href="#tab-incidence-docs" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.documentos')) !!}</a>
				  	</li>
				</ul>

				{{-- Panels --}}
				<div class="tab-content pt-4">
					{{-- Info del proyecto: --}}
					<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-incidence-info">
						@include('admin.incidences.partials.incidence-info')	
					</div>	
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
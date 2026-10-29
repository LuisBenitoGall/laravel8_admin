@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Volver a mensajes --}}
		@can('incidences.index')
			<a href="{{ route('incidences.edit', [$message->incidence_id, 'reel']) }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_mensajes') !!}
			</a>
		@endcan

		{{-- Responder --}}
		<a href="#" class="btn btn-info btnAddResponse" data-toggle="modal" data-target="#modal-new-message" data-id="{{ $message->id }}" rel="tooltip" title="{{ ucfirst(trans('textos.responder')) }}">
			<i class="fa fa-reply"></i>
			{{ trans('textos.responder') }}
		</a>
	</div>
</div>

<div class="row mb-3">
	<div class="col-12">						
		<div class="card">
			<div class="card-body">
				<h3 class="mb-3">
					{{ ucfirst(trans('textos.mensajes_incidencia')) }} 
					<u>
						{{ ucfirst($incidence->concept) }}
						@if($incidence->incidence_num)
							- <span class="text-warning">{{ $incidence->incidence_num }}</span>
						@endif
					</u>

					@if($message->corrective_action)
						<span class="badge badge-success mx-3">{{ trans('textos.accion_correctiva') }}</span>
					@endif

					@if($response)
						, {{ trans('textos.respuesta_mensaje') }}
						<a href="{{ route('incidence-messages.show', $response->id) }}">{{ $response->id }} {{ $response->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}</a>
					@endif
				</h3>

				{{-- Info --}}
				<div class="mb-3">
					{{-- Autor --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.autor')) !!}: <b>{{ $message->author->name }} {{ $message->author->surname }}</b>
					</label>

					{{-- Fecha alta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.fecha_alta')) !!}: 
						<b>
							{{ $message->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Fecha última actualización --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.ultima_edicion')) !!}: 
						<b>
							{{ $message->updated_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>
				</div>

				{{-- Tabs: --}}
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item @if(!$tab) active @endif">
			    		<a class="nav-link @if(!$tab) active @endif" href="#tab-message-info" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.mensaje')) !!}</a>
			  		</li>

				  	<li class="nav-item @if($tab == 'docs') active @endif">
				    	<a class="nav-link @if($tab == 'docs') active @endif" href="#tab-message-docs" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.documentos')) !!}</a>
				  	</li>
			  	</ul>

			  	{{-- Panels --}}
				<div class="tab-content pt-4">
					{{-- Mensaje --}}
					<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-message-info">
						@include('admin.incidence_messages.partials.message-responses')
					</div>

					{{-- Documentos --}}
					<div class="tab-pane fade @if($tab == 'docs') show active @endif" role="tabpanel" id="tab-message-docs">
						@include('admin.incidence_messages.partials.message-docs')	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

{{-- Modals: --}}
{{-- New message --}}
@include('admin.incidences.partials.modal-new-message', ['incidenceId' => $message->incidence_id])

@endsection
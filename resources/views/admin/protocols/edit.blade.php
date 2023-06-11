@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="dtProtocolId" value="{{ $protocol->id }}">
<input type="hidden" id="dtController" value="protocol-items">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Volver a protocolos --}}
		<a href="{{ route('protocols.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_protocolos') !!}
		</a>	

		{{-- Nuevo protocolo --}}
		@can('protocols.create')
    		<a href="{{ route('protocols.create') }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.protocolo_nuevo')) !!}
    		</a>
        @endcan

        {{-- Nuevo item --}}
        @can('protocols.edit')
    		<a href="{{ route('protocol-items.create', $protocol->id) }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.protocolo_item_nuevo')) !!}
    		</a>
        @endcan

        {{-- Agregar usuarios --}}
        @can('protocols.edit')
       		<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-add-users">
				<i class="fa fa-plus"></i>
				{!! trans('textos.usuarios_agregar') !!}
			</a> 	
        @endcan

        {{-- Imprimir --}}
		<a href="{{ route('protocols.print', $protocol) }}" class="btn btn-info close-animation" rel="tooltip" title="{!! ucfirst(trans('textos.imprimir')) !!}">
			<i class="fa fa-print"></i>
		</a>
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-3">{{ trans('textos.protocolo') }} <u>{{ $protocol->name }}</u></h3>

				{{-- Info --}}
				<div class="mb-3">
					{{-- Fecha alta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.fecha_alta')) !!}:
						<b>
							{{ $protocol->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Fecha última actualización --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.ultima_edicion')) !!}:
						<b>
							@if($protocol->updated_at) {{ $protocol->updated_at->format($languages[session()->get('locale')][4].' H:i:s') }} @endif
						</b>
					</label>

					{{-- Autor --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.autor')) !!}:
						<b>
							@if($author)
								{{ ucfirst($author->name) }} {{ ucfirst($author->surname) }}
							@endif
						</b>
					</label>
				</div>

				{{-- Tabs --}}
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item @if(!$tab) active @endif">
			    		<a class="nav-link @if(!$tab) active @endif" href="#tab-info" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.info_general')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'items') active @endif">
			  			<a class="nav-link @if($tab == 'items') active @endif" href="#tab-items" role="tab" data-toggle="tab">Items</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'users') active @endif">
			  			<a class="nav-link @if($tab == 'users') active @endif" href="#tab-users" role="tab" data-toggle="tab">{{ ucfirst(trans('textos.usuarios')) }}</a>
			  		</li>
			  	</ul>

			  	<div class="tab-content pt-4">
			  		{{-- Información general --}}
			  		<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-info">
						@include('admin.protocols.partials.protocol-info')
					</div>

					{{-- Items --}}
					<div class="tab-pane fade @if($tab == 'items') show active @endif" role="tabpanel" id="tab-items">
						@include('admin.protocols.partials.protocol-items')
					</div>

					{{-- Usuarios --}}
					<div class="tab-pane fade @if($tab == 'users') show active @endif" role="tabpanel" id="tab-users">
						@include('admin.protocols.partials.protocol-users')
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

{{-- Modals: --}}
{{-- Add users --}}
@can('protocols.edit')
	@include('admin.protocols.partials.modal-add-users')
@endcan

@endsection
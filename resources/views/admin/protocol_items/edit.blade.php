@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Volver al protocolo --}}
		<a href="{{ route('protocols.edit', [$protocol, 'items']) }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_a') !!} {{ $protocol->name }}
		</a>	

		{{-- Nuevo item --}}
        @can('protocols.edit')
    		<a href="{{ route('protocol-items.create', $protocol->id) }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.protocolo_item_nuevo')) !!}
    		</a>
        @endcan

        {{-- Galería de archivos --}}
		<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-gallery">
			<i class="fa fa-image"></i> {!! ucfirst(trans('textos.galeria_archivos')) !!}
		</a>
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-3">{{ trans('textos.protocolo') }} <u>{{ $protocol->name }}</u> - Item <u>{{ $item->title }}</u></h3>

				{{-- Info --}}
				<div class="mb-3">
					{{-- Fecha alta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.fecha_alta')) !!}:
						<b>
							{{ $item->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Fecha última actualización --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.ultima_edicion')) !!}:
						<b>
							@if($item->updated_at) {{ $item->updated_at->format($languages[session()->get('locale')][4].' H:i:s') }} @endif
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

				<form method="post" autocomplete="off" action="{{ route('protocol-items.update', $item) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Título --}}
						<label for="title" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.titulo')) !!}</label>
						<div class="col-lg-6">
							<x-input type="text" name="title" placeholder="{!! ucfirst(trans('textos.titulo')) !!}" maxlength="150" value="{{ $item->title }}"/>
						</div>

						{{-- Estado --}}
						<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.estado')) !!}</label>
						<div class="col-lg-2">
							<x-select name="status" class="select2" label="estado_selec" :options="$estados" value="{{ $item->status }}" info=""/>
						</div>
					</div>

					<div class="form-group row">		
						{{-- Descripción --}}
						<label for="description" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.descripcion')) !!}</label>
						<div class="col-lg-10">
							<x-input-textarea name="description" class="editor" value="{!! $item->description !!}"/>
						</div>	
					</div>


					<x-btn-submit name="submit_update" classdiv="col-12 text-right" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

{{-- Modals --}}
{{-- Galería de archivos --}}
@include('admin.files.partials.modal-gallery')

@endsection
@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Volver a protocolos --}}
		<a href="{{ route('protocols.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_protocolos') !!}
		</a>	
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
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
							{{ $protocol->author->name }} {{ $protocol->author->surname }}
						</b>
					</label>
				</div>

				<h1 class="mb-5"><u>{{ $protocol->name }}</u></h1>

				@foreach($items as $i)
					<h3>{{ $i->title }}</h3>
					<div class="mb-4">{!! $i->description !!}</div>
				@endforeach
			</div>
		</div>
	</div>
</div>

@endsection
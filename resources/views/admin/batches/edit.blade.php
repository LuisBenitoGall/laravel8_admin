@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones --}}
	<div class="col-12">
		{{-- Lotes --}}
		@can('batches.index')
			<a href="{{ route('batches.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_lotes') !!}
			</a>
		@endcan

		@can('batches.create')
			<a class="btn btn-info" href="{{ route('batches.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.lote_nuevo')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-3">{{ ucfirst(trans('textos.lote')) }} <u>{{ $batch->name }}</u></h3>

				{{-- Info --}}
				<div class="mb-3">
					{{-- Fecha alta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.fecha_alta')) !!}:
						<b>
							{{ $batch->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Fecha última actualización --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.ultima_edicion')) !!}:
						<b>
							@if($batch->updated_at) {{ $batch->updated_at->format($languages[session()->get('locale')][4].' H:i:s') }} @endif
						</b>
					</label>
				</div>

				<form method="post" autocomplete="off" action="{{ route('batches.update', $batch) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Nombre lote --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.lote')) !!}*</label>
						<div class="col-lg-4">
							<x-input type="text" name="name" placeholder="{!! ucfirst(trans('textos.banco')) !!}" autocomplete="off" maxlength="150" required value="{{ $batch->name }}"/>
						</div>	


					</div>

					<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection
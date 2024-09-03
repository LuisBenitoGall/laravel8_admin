@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		<a href="{{ route('dealers.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_repartidores') !!}
		</a>
	</div>
</div>

<div class="row mb-3">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-4">{!! trans('textos.repartidor') !!} <u>{{ ucwords($user->name) }} {{ ucwords($user->surname) }}</u></h3>

			</div>
		</div>
	</div>
</div>

@endsection
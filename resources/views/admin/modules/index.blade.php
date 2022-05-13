@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('modules.create')
			<a href="{{ route('modules.create') }}" class="btn btn-info">
				<i class="fa fa-plus"></i>
				{!! ucfirst(trans('textos.modulo_nuevo')) !!}
			</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblModules">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.modulo')) !!}</th>
					<th>{!! ucfirst(trans('textos.nivel')) !!}</th>
	                <th class="wdt50">{!! ucfirst(trans('textos.icono')) !!}</th>
	                <th class="wdt50">{!! ucfirst(trans('textos.color')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div> 
</div>

@endsection
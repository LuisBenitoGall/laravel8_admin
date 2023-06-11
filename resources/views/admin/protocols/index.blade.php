@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="dtController" value="protocols">

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('protocols.create')
    		<a href="{{ route('protocols.create') }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.protocolo_nuevo')) !!}
    		</a>
        @endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblProtocols">
			<thead>
				<tr>
					<td>{{ ucfirst(trans('textos.protocolo')) }}</td>
					<td class="text-center">{{ ucfirst(trans('textos.fecha_alta')) }}</td>
					<td>{{ ucfirst(trans('textos.autor')) }}</td>
					<td>{{ ucfirst(trans('textos.estado')) }}</td>
					<td class="text-center">{{ ucfirst(trans('textos.acciones')) }}</td>
				</tr>
			</thead>
		</table>
	</div>
</div>

@endsection
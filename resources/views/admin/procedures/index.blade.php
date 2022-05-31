@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Nuevo procedimiento --}}
		@can('procedures.create')
    		<a href="{{ route('procedures.create') }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.procedimiento_nuevo')) !!}
    		</a>
        @endcan

	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblProcedures">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.referencia')) !!}</th>
					<th>{!! ucfirst(trans('textos.procedimiento')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.ultima_revision')) !!}</th>
					<th>{!! ucfirst(trans('textos.responsable')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@endsection
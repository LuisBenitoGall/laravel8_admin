@extends('admin.partials.layout')

@section('content')

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblQuizEditions">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.edicion')) !!}</th>
					<th>{!! ucfirst(trans('textos.concurso')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.logo')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.inicio')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fin')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.duracion')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@endsection
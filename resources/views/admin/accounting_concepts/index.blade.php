@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		@can('accounting-concepts.create')
			<a class="btn btn-info" href="{{ route('accounting-concepts.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.concepto_nuevo')) !!}</a>
		@endcan
	</div>	
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblAccountingConcepts">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.concepto')) !!}</th>
					<th>{!! ucfirst(trans('textos.nivel')) !!}</th>
					<th>{!! ucfirst(trans('textos.descripcion')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@endsection
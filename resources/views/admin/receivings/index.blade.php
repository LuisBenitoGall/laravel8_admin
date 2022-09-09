@extends('admin.partials.layout')

@section('content')

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblReceivings">
			<thead>
				<tr>
					<th class="text-center">{!! ucfirst(trans('textos.recepcion_fecha_prevista')) !!}</th>
					<th>{!! ucfirst(trans('textos.proveedor')) !!}</th>
					<th>{!! ucfirst(trans('textos.pedido')) !!}</th>
					<th>{!! ucfirst(trans('textos.albaran')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@endsection
<div class="table-responsive">
	<p class="text-warning">{{ trans('textos.pedidos_pendientes_entrega') }}</p>
	<table class="table table-hover dtabla" id="tblDeliveriesOrders">
		<thead>
			<tr>
				<th class="text-center">#</th>
				<th>{{ ucfirst(trans('textos.pedido')) }}</th>
				<th>{!! ucfirst(trans('textos.cliente')) !!}</th>
				<th>{!! ucfirst(trans('textos.estado')) !!}</th>
				<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
			</tr>
		</thead>
	</table>
</div>
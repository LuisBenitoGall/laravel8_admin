<div class="row">
	@if(count($stores) == 0)
		<p class="col-12 text-danger">
			{!! trans('textos.almacen_consumo_interno_inactivo_aviso') !!}
		</p>
	@endif

	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblProductSerials">
			<thead>
				<tr>
					<th>{{ ucfirst(trans('textos.numero_serie')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.fecha')) }}</th>
					<th>{{ ucfirst(trans('textos.empleado')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.fecha_asignacion')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.acciones')) }}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>
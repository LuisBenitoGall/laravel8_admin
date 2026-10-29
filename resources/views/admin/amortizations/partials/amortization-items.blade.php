<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblAmortizationItems">
			<thead>
				<tr>
					<th class="text-center">{{ ucfirst(trans('textos.fecha')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.importe')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.porcentaje')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.acciones')) }}</th>
				</tr>
			</thead>

			<tfoot>	
				<tr>
					<td class="text-right">{{ trans('textos.amortizado_total') }}</td>
					<td>{{ $amortized }}{{ $currency->symbol }}</td>
					<td colspan="2"></td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>
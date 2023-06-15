<div class="table-responsive">
    <table class="table table-hover dtabla" id="tblOrderItems">
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th>{{ ucfirst(trans('textos.producto')) }}</th>
                <th class="text-center">{{ ucfirst(trans('textos.cantidad')) }}</th>
                <th class="text-center">{{ ucfirst(trans('textos.precio_unidad')) }}</th>
                <th class="text-center">{{ ucfirst(trans('textos.jornadas')) }}</th>
                <th class="text-center">{{ ucfirst(trans('textos.descuento')) }}</th>
                <th class="text-center">{{ ucfirst(trans('textos.importe')) }}</th>
                <th>{{ ucfirst(trans('textos.centro_coste')) }}</th>
                <th>{{ ucfirst(trans('textos.estado')) }}</th>
                <th class="text-center">{{ ucfirst(trans('textos.acciones')) }}</th>
            </tr>
        </thead>

        <tfoot>
            @if($order->discount)
                <tr>
                    <td colspan="6" class="text-right"><b>{{ ucfirst(trans('textos.descuento')) }}</b></td>
                    <td class="text-right">{{ $order->discount }}%</td>
                    <td></td>
                    <td></td>
                </tr>
            @endif

            <tr>
                <td colspan="6" class="text-right"><b>{{ trans('textos.importe_total') }}</b></td>
                <td class="text-right">{{ round($order_amount, 2) }}{{ $currency->symbol }}</td>
                <td></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>
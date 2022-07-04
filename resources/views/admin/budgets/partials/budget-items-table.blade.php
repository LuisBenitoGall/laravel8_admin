<div class="table-responsive">
    <table class="table table-hover dtabla" id="tblOrderItems">
        <thead>
            <tr>
                <td class="text-center">#</td>
                <td>{{ ucfirst(trans('textos.producto')) }}</td>
                <td class="text-center">{{ ucfirst(trans('textos.cantidad')) }}</td>
                <td class="text-center">{{ ucfirst(trans('textos.precio_unidad')) }}</td>
                <td class="text-center">{{ ucfirst(trans('textos.jornadas')) }}</td>
                <td class="text-center">{{ ucfirst(trans('textos.descuento')) }}</td>
                <td class="text-center">{{ ucfirst(trans('textos.importe')) }}</td>
                <th>{{ ucfirst(trans('textos.estado')) }}</th>
                <td class="text-center">{{ ucfirst(trans('textos.acciones')) }}</td>
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
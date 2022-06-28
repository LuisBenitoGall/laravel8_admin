<div class="float-fixed-div">
    <div class="row">
        <div class="col-12">
            @if($environment == 'budgets')
                {!! trans('textos.importe_presupuesto') !!}
            @elseif($environment == 'orders')
                {!! trans('textos.importe_pedido') !!}
            @elseif($environment == 'invoices')
                {!! trans('textos.importe_factura') !!}
            @endif

            <span class="totalAmount">
                {{ number_format($budget_amount, 2) }}{{ $currency }}
            </span>
            <input type="hidden" id="totalAmount_"> 

            {{-- Bolsa --}}
            @if($bag_amount)
                {!! ucfirst(trans('textos.bolsa')) !!}
                <br>  
                <b>
                    {{ number_format($bag_amount, 2) }}{{ $currency }}
                </b>  
                <br>

                {!! ucfirst(trans('textos.pendiente')) !!}
                <br> 
                <b>
                    {{ number_format($bag_amount - $budget_amount, 2) }}{{ $currency }}   
                </b>
            @endif

            @if($budget_amount_without_discount && $budget_amount_without_discount > $budget_amount)
                {!! trans('textos.importe_sin_descuento') !!}
                <br>
                <b>
                    {{ number_format($budget_amount_without_discount, 2) }}{{ $currency }}
                </b>
            @endif
        </div>
    </div>
</div>
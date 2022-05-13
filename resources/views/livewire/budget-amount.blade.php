<div class="float-fixed-div">
    <div class="row">
        <div class="col-12">
            @if($environment == 'budgets')
                {!! trans('textos.importe_presupuesto') !!}
            @elseif($environment == 'orders')
                {!! trans('textos.importe_pedido') !!}
            @endif

            <span class="totalAmount">
                {{ $budget_amount }}{{ $currency }}
            </span>
            <input type="hidden" id="totalAmount_"> 

            @if($budget_amount_without_discount && $budget_amount_without_discount > 0)
                {!! trans('textos.importe_sin_descuento') !!}
                <br>
                <b>
                    {{ $budget_amount_without_discount }}{{ $currency }}
                </b>
            @endif
        </div>
    </div>
</div>
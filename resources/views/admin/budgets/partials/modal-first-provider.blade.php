<div class="modal fade modal-first-provider" id="modal-first-provider" tabindex="-1" role="dialog" aria-labelledby="linkBudgetProvider" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="linkBudgetProvider">{!! ucfirst(trans('textos.presupuesto_proveedor_vincular')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
            	<p class="text-warning">{!! trans('textos.presupuesto_vincular_aviso') !!}</p>
            	<form method="post" autocomplete="off" action="{{ route('budgets.link-provider-budget') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="order_id" value="{{ $order->id }}">

                    {{-- Presupuestos --}}
                    <div class="form-group row">
                        <label for="budget_id" class="col-lg-12">{!! ucfirst(trans('textos.presupuestos')) !!}*</label>
                        <div class="col-lg-12"> 
                            <x-select name="budget_id" class="select2" label="presupuesto_selec" :options="$linkable_budgets" required/>
                        </div>
                    </div>

                    <x-btn-submit name="submit_link" label="vincular"/>
                </form>
            </div>
        </div>
    </div>
</div>
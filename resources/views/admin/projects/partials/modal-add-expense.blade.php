<div class="modal fade modal-add-expense" id="modal-add-expense" tabindex="-1" role="dialog" aria-labelledby="addexpense" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addPurchase">{!! ucfirst(trans('textos.gastos_add')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('expenses.link-projects') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="project_id" value="{{ $project->id }}">

                    <div class="form-group row">
                        {{-- Gastos --}}
                        <label for="orders" class="col-12">{{ ucfirst(trans('textos.gastos')) }}*</label>
                        <div class="col-12">
                            @php
                            $expenses_ = [];

                            foreach($expenses as $expense){
                                $concept = App\Helpers\helpers::truncate($expense->concept, 40);
                                $expenses_[$expense->id] = $concept.' - '.$expense->amount_with_iva.$currency->symbol;
                            }
                            @endphp

                            <x-select name="expenses[]" class="select2" :options="$expenses_" multiple required/>
                        </div>
                    </div>

                    <x-btn-submit name="submit_add_expenses" label="{{ trans('textos.guardar') }}"/>
                </form>   
            </div>
        </div>
    </div>
</div>
<div class="modal fade modal-modality" id="modal-modality" tabindex="-1" role="dialog" aria-labelledby="setModality" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="setModality">{!! ucfirst(trans('textos.presupuesto_modalidad')) !!}</h4>
            </div>

            <div class="modal-body">
                <p class="text-warning">{{ trans('textos.presupuesto_modalidad_aviso') }}</p>

                <form method="post" autocomplete="off" action="{{ route('budgets.set-modality') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                    <input type="hidden" name="customer" id="formCustomerId" value="{{ $order->customer_id }}">
                    <input type="hidden" id="numForms" value="{{ $forms->count() }}">

                    <div class="form-group row">
                        {{-- Modalidad --}}
                        <label for="modality" class="col-lg-12">{!! ucfirst(trans('textos.modalidad')) !!}*</label>
                        <div class="col-lg-12">
                            @php
                            $modalities = App\Helpers\Helpers::budgetModalities();
                            @endphp

                            <x-select name="modality" id="selectBudgetModality" class="select2" label="modalidad_selec" :options="$modalities" info="budget-modality" required/>
                        </div>
                    </div>

                    {{-- Mensaje: No hay formularios --}}
                    <div id="noBudgetForms">
                        <div class="form-group row">
                            <label class="col-12 text-center text-warning mt-4"><b>{!! ucfirst(trans('textos.formularios_no_hay')) !!}</b> <a href="{{ route('budget-forms.index') }}" class="btn btn-info ml-4">{!! ucfirst(trans('textos.formulario_crear')) !!}</a></label>
                        </div>
                    </div>

                    {{-- Selecciona formulario --}}
                    <div id="budgetForms">
                        <div class="form-group row">
                            <label for="form_id" class="col-12">{!! ucfirst(trans('textos.formulario')) !!} *</label>
                            <div class="col-12">
                                @php
                                $forms_ = [];
                                @endphp

                                @foreach($forms as $form)
                                    @php
                                    $forms_[$form->id] = $form->name;
                                    @endphp
                                @endforeach

                                <x-select name="form_id" id="budgetForm" class="select2" label="formulario_selec" :options="$forms_"/>
                            </div>
                        </div>
                    </div>

                    <x-btn-submit name="submit_set_modality" label="{{ trans('textos.guardar') }}"/>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade modal-import-budget" id="modal-import-budget" tabindex="-1" role="dialog" aria-labelledby="importBudget" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="importBudget">{!! ucfirst(trans('textos.presupuesto_importar')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
            	<p class="text-warning">{!! trans('textos.presupuesto_importar_aviso') !!}</p>
            	<form method="post" autocomplete="off" action="{{ route('budgets.import') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" value="{{ $order->id }}">
                    <input type="hidden" id="numForms" value="{{ $forms->count() }}">

                    {{-- Presupuesto --}}
                    <div class="form-group row">
                    	<label for="budget_id" class="col-lg-12">{!! ucfirst(trans('textos.presupuestos')) !!}</label>
	                    <div class="col-lg-12">	
	                    	<x-select name="budget_id" class="select2" label="presupuesto_selec" :options="$customer_budgets"/>
	                    </div>
                    </div>
                    <hr>

                    <p class="text-warning">{!! trans('textos.presupuesto_importar_aviso2') !!}</p>

                    {{-- Modalidad --}}
                    <div class="form-group row">
                    	<label for="modality" class="col-lg-12">{!! ucfirst(trans('textos.modalidad')) !!}</label>
						<div class="col-lg-12">
							@php
							$modalities = App\Helpers\helpers::budgetModalities();
							@endphp

							<x-select name="modality" class="select2" id="selectBudgetModality" label="modalidad_selec" :options="$modalities"/>
						</div>
                    </div>

                    {{-- Formulario --}}
                    @if($forms->count() == 1)
                    	<input type="hidden" name="form_id" value="{{ $forms[0]->id }}">

                    @elseif($forms->count() > 1)
	                    <div class="form-group row" @if(!$errors->has('form_id')) id="budgetForms" @endif>
	                    	<label for="form_id" class="col-lg-12">{!! ucfirst(trans('textos.formulario')) !!}</label>
							<div class="col-lg-12">
								@php
								$forms_ = [];
								foreach($forms as $f){
									$forms_[$f->id] = $f->name;
								}
								@endphp

								<x-select name="form_id" class="select2" id="budgetForm" label="formulario_selec" :options="$forms_"/>
							</div>
						</div>
					@endif

               		<x-btn-submit name="submit_import" label="{{ trans('textos.importar') }}"/>
                </form>
            </div>
        </div>
    </div>
</div>
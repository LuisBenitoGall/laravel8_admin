<div class="modal fade modal-edit-config" id="modal-edit-config" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{!! ucfirst(trans('textos.configuracion')) !!} <b></b></h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
            	<div class="row">
            		<p class="col-12 text-warning">{!! trans('textos.establece_por_defecto_texto') !!}</p>
            	</div>

                <form method="post" action="{{ route('customers.config') }}" autocomplete="off" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="customer_id" id="configCustomer">

                    {{-- Área de negocio --}}
                    <div class="form-group row">
                        <label for="ba_id" class="col-lg-12">{!! ucfirst(trans('textos.area_negocio')) !!}</label>
                        <div class="col-lg-12">
                            <x-select name="ba_id" class="select2" id="configBusinessArea" :options="[]"/>
                        </div>
                    </div>

                    {{-- Centro de trabajo --}}
                    <div class="form-group row">
                        <label for="workplace_id" class="col-lg-12">{!! ucfirst(trans('textos.centro_trabajo')) !!}</label>
                        <div class="col-lg-12">
                            <x-select name="workplace_id" class="select2" id="configWorkplace" :options="[]"/>
                        	</select>	
                        </div>
                    </div>

                    {{-- Centro de coste --}}
                    <div class="form-group row">
                        <label for="cc_id" class="col-lg-12">{!! ucfirst(trans('textos.centro_coste')) !!}</label>
                        <div class="col-lg-12">
                            <x-select name="cc_id" class="select2" id="configCostCenter" :options="[]"/>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        {{-- Descuento sobre pedidos --}}
                        <div class="col-md-6">
                            <div class="row">
                                <label for="discount" class="col-12">{!! ucfirst(trans('textos.descuento_pedidos')) !!}</label>
                                <div class="col-12">
                                    <x-input type="text" name="discount" class="text-right set-decimals" id="configDiscount" autocomplete="off" maxlength="5" group="right" groupvalue="%" info="customer-discount"/>
                                </div>
                            </div>
                        </div>

                        {{-- Acceso de cliente a presupuestos --}}
                        <div class="col-md-6">
                            <div class="row">
                                <label for="budget_access" class="col-12">{!! ucfirst(trans('textos.presupuesto_acceso')) !!}</label>
                                <div class="col-9 offset-2 pt-1">
                                    <label class="custom-chk-rdb">
                                        <input type="checkbox" name="budget_access" id="configBudgetAccess">
                                        <span class="chk"></span>
                                    </label>

                                    <div class="position-absolute" style="left: 65px; top: 0;">
                                        {!! App\Helpers\helpers::info('budget-customer-access') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Riesgo crediticio --}}
                        <div class="col-md-6">
                            <div class="row">
                                <label for="credit_risk" class="col-12">{!! ucfirst(trans('textos.riesgo_crediticio')) !!}</label>
                                <div class="col-12">
                                    <x-input type="text" name="credit_risk" class="text-right set-decimals" id="configCreditRisk" autocomplete="off" maxlength="5" group="right" groupvalue="{{ $currency->symbol }}" info="customer-credit-risk"/>
                                </div>
                            </div>
                        </div>

                        {{-- Sincronización previsiones de facturación entre proveedor y cliente --}}
                        <div class="col-md-6">
                            <div class="row">
                                <label for="syncro_invoice" class="col-12">{!! ucfirst(trans('textos.facturacion_sincronizar')) !!}</label>
                                <div class="col-9 offset-2 pt-1">
                                    <label class="custom-chk-rdb">
                                        <input type="checkbox" name="syncro_invoice" id="configSyncroInvoice">
                                        <span class="chk"></span>
                                    </label>

                                    <div class="position-absolute" style="left: 65px; top: 0;">
                                        {!! App\Helpers\helpers::info('budget-syncro-invoice') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <x-btn-submit name="submit_update_config" label="{{ trans('textos.guardar') }}"/>
                </form>
            </div>
        </div>
    </div>
</div>
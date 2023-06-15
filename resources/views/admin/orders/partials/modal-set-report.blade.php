<div class="modal fade modal-set-report" id="modal-set-report" tabindex="-1" role="dialog" aria-labelledby="setReport" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="setReport">{!! ucfirst(trans('textos.informes')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
            	<form method="post" autocomplete="off" action="{{ route('orders.export-by-range-dates') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="customer_id" value="@if($customer_id && $customer_id != 'all') {{ $customer->id }} @endif">
                    <input type="hidden" name="provider_id" value="@if($provider) {{ $provider->id }} @endif">

                    <div class="row">
                    	<p class="col-12 text-warning">{!! trans('textos.informe_pedidos_rango_fechas') !!}</p>
                    </div>

                    <div class="form-group row">
                        {{-- Desde --}}
						<label for="from" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.desde')) !!}*</label>
						<div class="col-lg-4">
							<x-date-picker name="from" :format="$languages[config('app.locale')][4]"/>
						</div>

                        {{-- Hasta --}}
						<label for="to" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.hasta')) !!}*</label>
						<div class="col-lg-4">
					        <x-date-picker name="to" :format="$languages[config('app.locale')][4]"/>	
						</div>
					</div>

					<x-btn-submit name="submit_set_report" label="{{ trans('textos.buscar') }}"/>
                </form>
            </div>
        </div>
    </div>
</div>
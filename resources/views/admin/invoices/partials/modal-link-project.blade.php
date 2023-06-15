<div class="modal fade modal-link-project" id="modal-link-project" tabindex="-1" role="dialog" aria-labelledby="generateRefund" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{!! ucfirst(trans('textos.factura_asociar_proyecto')) !!} <b></b></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('project-purchases.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="orders[]" value="{{ $order->id }}">
                    @if(isset($invoice) && $invoice)
                        <input type="hidden" name="invoice" value="{{ $invoice->id }}">
                    @endif
                    @if(isset($return_) && $return_)
                        <input type="hidden" name="return" value="{{ $order->id }}">
                    @endif

                    <p class="text-warning">{{ trans('textos.factura_asociar_proyecto_texto') }}</p>

                    <div class="form-group row mt-4">
                        {{-- Empresas --}}
                        <label for="company" class="col-12">{!! ucfirst(trans('textos.empresa')) !!}*</label>
                        <div class="col-12">
                            @php
                            $companies_ = [];
                            foreach(session('companies') as $c){
                                $companies_[$c->id] = $c->tradename? $c->tradename:$c->name;
                            }
                            @endphp

                            <x-select name="company" class="select2" id="companyId_" label="empresa_selec" :options="$companies_" />
                        </div>  
                    </div>

                    <div class="form-group row">
                        {{-- Proyectos --}}
                        <label for="project_id" class="col-12">{!! ucfirst(trans('textos.proyectos')) !!}*</label>
                        <div class="col-12">
                            <x-select name="project_id" class="select2" id="projectId_" :options="[]" disabled/>
                        </div>
                    </div>

                    <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                </form>
            </div>
        </div>
    </div>
</div>
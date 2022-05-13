<div class="modal fade modal-add-purchase-to-project" id="modal-add-purchase-to-project" tabindex="-1" role="dialog" aria-labelledby="addPurchaseToProject" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addPurchaseToProject">{!! ucfirst(trans('textos.proyecto_vincular_compra')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('orders.add-purchase-to-project') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="order_id" value="{{ $order->id }}">

                    <div class="form-group row">
                        {{-- Proyectos --}}
                        <label for="project_id" class="col-12">{!! ucfirst(trans('textos.proyectos')) !!}*</label>
                        <div class="col-12">
                            @php
                            $projects_ = [];
                            @endphp

                            @foreach($projects as $project)
                                @php
                                $projects_[$project->id] = $project->name;
                                @endphp
                            @endforeach

                            <x-select name="project_id" class="select2" :options="$projects_" required label="proyecto_selec"/>
                        </div>
                    </div>

                    <x-btn-submit name="submit_add_purchase" label="{{ trans('textos.guardar') }}"/>
                </form>
            </div>
        </div>
    </div>
</div>
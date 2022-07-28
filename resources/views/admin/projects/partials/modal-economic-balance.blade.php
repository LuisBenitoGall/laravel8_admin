<div class="modal fade modal-economic-balance" id="modal-economic-balance" tabindex="-1" role="dialog" aria-labelledby="modalEconomicBalance" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalEconomicBalance">{!! ucfirst(trans('textos.balance_economico')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            {{-- Componente Projects Economic Balance --}}
            @livewire('projects-economic-balance', ['project' => $project])
        </div>
    </div>
</div>

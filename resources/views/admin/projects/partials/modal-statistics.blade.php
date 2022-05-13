<div class="modal fade modal-statistics" id="modal-statistics" tabindex="-1" role="dialog" aria-labelledby="modalStatistics" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalStatistics">{!! ucfirst(trans('textos.estadisticas')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            {{-- Componente Projects Statistics --}}
            @livewire('projects-statistics')
        </div>
    </div>
</div>

<div class="modal fade" id="modal-gallery" tabindex="-1" role="dialog" aria-labelledby="gallery" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="gallery">{!! ucfirst(trans('textos.galeria_archivos')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

			<div class="modal-body">
				@livewire('files-gallery')
			</div>
        </div>
    </div>
</div>
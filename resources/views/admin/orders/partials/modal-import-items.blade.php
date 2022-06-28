<div class="modal fade modal-import-items" id="modal-import-items" tabindex="-1" role="dialog" aria-labelledby="importItems" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="importItems">{!! ucfirst(trans('textos.lineas_importar')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
            	<p class="text-warning">
            		{{ trans('textos.lineas_importar_texto') }}
            		<a href="{!! asset('downloads/template_import_order_items.xlsx') !!}" class="text-info" download><i class="fa fa-file-excel ml-2"></i></a>
            	</p>

                <form method="post" autocomplete="off" action="{{ route('items.import-items') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="order_id" value="{{ $order->id }}">

					<div class="form-group row">
						{{-- Archivo .xls --}}
                    	<label for="items" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.archivo')) !!}*</label>
						<div class="col-lg-10">
							<x-input-file-xls name="items"/>
						</div>
					</div>

					<x-btn-submit name="submit_import_items" label="{{ trans('textos.importar') }}"/>
                </form>    
            </div>
        </div>
    </div>
</div>
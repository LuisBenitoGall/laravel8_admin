<div class="modal fade modal-edit-town" id="modal-edit-town" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{!! ucfirst(trans('textos.provincia_editar')) !!}</h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
				<form method="post" action="{{ route('towns.update') }}" autocomplete="off" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}
					<input type="hidden" name="province_id" value="{{ $province->id }}">
					<input type="hidden" name="id" id="townId">

					<div class="form-group row">
						<div class="col-lg-9">
							<div class="row">
								{{-- Población --}}
								<label for="name" class="col-lg-12">{!! ucfirst(trans('textos.provincia')) !!} *</label>
								<div class="col-lg-12">
									<x-input type="text" name="name" id="townName" maxlength="191" required/>
								</div>
							</div>
						</div>

						<div class="col-lg-3">
							<div class="row">
								{{-- Status --}}
								<label for="status" class="col-lg-12">{!! ucfirst(trans('textos.estado')) !!}</label>
								<div class="col-lg-12 text-center">
									<label class="custom-chk-rdb">
						        		<input type="checkbox" name="status" id="townStatus">
						        		<span class="chk" style="top: 5px;"></span>
						    		</label>
								</div>
							</div>
						</div>
					</div>
					
					<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
        </div>
    </div>
</div>
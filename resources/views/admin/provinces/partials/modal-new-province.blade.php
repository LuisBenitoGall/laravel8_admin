<div class="modal fade modal-new-province" id="modal-new-province" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{!! ucfirst(trans('textos.provincia_nueva')) !!}</h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
				<form method="post" action="{{ route('provinces.store') }}" autocomplete="off" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}
					<input type="hidden" name="country_id" value="{{ $country->id }}">

					<div class="form-group row">
						<div class="col-lg-9">
							<div class="row">
								{{-- Provincia --}}
								<label for="name" class="col-lg-12">{!! ucfirst(trans('textos.provincia')) !!} *</label>
								<div class="col-lg-12">
									<x-input type="text" name="name" maxlength="191" required/>
								</div>
							</div>
						</div>

						<div class="col-lg-3">
							<div class="row">
								{{-- Status --}}
								<label for="status" class="col-lg-12">{!! ucfirst(trans('textos.estado')) !!}</label>
								<div class="col-lg-12 text-center">
									<label class="custom-chk-rdb">
						        		<input type="checkbox" name="status" checked>
						        		<span class="chk" style="top: 5px;"></span>
						    		</label>
								</div>
							</div>
						</div>
					</div>
					
					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
        </div>
    </div>
</div>
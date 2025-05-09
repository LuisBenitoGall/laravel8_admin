<div class="modal fade modal-add-customer" id="modal-add-customer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">{!! ucfirst(trans('textos.cliente_anadir')) !!}</h4>

				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<div class="modal-body">
				<form method="post" action="{{ route('tariff-companies.store') }}" autocomplete="off" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}
					<input type="hidden" name="tariff_id" value="{{ $tariff->id }}">

					<div class="row form-group">
						{{-- Clientes --}}
						<label for="customer" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cliente')) !!}*</label>
						<div class="col-lg-10">
							<input type="text" name="customer_search" class="form-control customer_search" data-type="name" id="search_customer" placeholder="{!! trans('textos.cliente_buscar') !!}" autocomplete="off" required/>
							<x-input type="hidden" name="customer_id" id="customerId" placeholder="" autocomplete="off" required/>
						</div>
					</div>

					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>
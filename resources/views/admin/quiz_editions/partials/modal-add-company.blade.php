<div class="modal fade modal-add-company" id="modal-add-company" tabindex="-1" role="dialog" aria-labelledby="addCompany" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="addCompany">{{ trans('textos.empresas_add') }} <u></u></h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<div class="modal-body">
				<form method="post" autocomplete="off" action="{{ route('quiz-edition-companies.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}	
					<input type="hidden" name="quiz_edition_id" value="{{ $edition->id }}">

					<div class="form-group row">
						<label class="col-12">{{ ucfirst(trans('textos.empresas')) }}*
							</label>
						<div class="col-12">
							<x-select name="companies[]" class="select2" multiple="true" :options="$companies" required info="quiz-edition-companies"/>
						</div>
					</div>

					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>
<div class="modal fade modal-add-team" id="modal-add-team" tabindex="-1" role="dialog" aria-labelledby="addTeam" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="addTeam">{{ trans('textos.equipo_add') }} <u></u></h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<div class="modal-body">
				<form method="post" autocomplete="off" action="{{ route('quiz-edition-teams.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}	
					<input type="hidden" name="quiz_edition_id" value="{{ $edition->id }}">

					<div class="form-group row">
						<label class="col-12">{{ ucfirst(trans('textos.equipos')) }} *
							</label>
						<div class="col-12">
							@php
							$options = [];
							@endphp

							@foreach($teams as $team)
								@php
								$options[$team->id] = $team->name;
								@endphp
							@endforeach

							<x-select name="teams[]" class="select2" multiple="true" :options="$options" required info="quiz-edition-teams"/>
						</div>
					</div>

					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dTable" id="tblQuizManagers_">
			<thead>
				<tr>
					<th>{{ ucfirst(trans('textos.manager')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.propietario')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.acciones')) }}</th>
				</tr>
			</thead>

			<tbody>
				<tr>
					<td>
						{{ $owner->name }} {{ $owner->surname }}
					</td>
					<td class="text-center">
						<i class="fa fa-check text-success"></i>
					</td>
					<td></td>
				</tr>

				@foreach($managers as $m)
					<tr>
						<td>
							{{ $m->name }} {{ $m->surname }}
						</td>
						<td></td>
						<td class="text-right">
							{{-- Desvincular --}}
							<a href="{{ route('quizzes.delete-manager', [$quiz->id, $m->id]) }}" class="btn btn-xs btn-info btn-danger btn-confirm" data-msg="{{ trans('textos.manager_eliminar_confirm') }}" rel="tooltip" title="{{ ucfirst(trans('textos.desvincular')) }}"><i class="fa fa-trash"></i></a>
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
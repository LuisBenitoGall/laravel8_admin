<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dTable" id="tblProjectTeam">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.miembro')) !!}</th>
					<!-- <th class="text-center">{!! ucfirst(trans('textos.inicio')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fin')) !!}</th>
					<th>{!! ucfirst(trans('textos.puesto')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.contrato_estado')) !!}</th> -->
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>

			<tbody>
				@foreach($team_members as $tm)
					<tr>
						<td>{{ $tm->name }} {{ $tm->surname }}</td>
						<td class="text-right">
							{{-- Tareas del miembro --}}
							<a href="#" class="btn btn-xs btn-info btnMemberTask" data-user="{{ $tm->id }}" rel="tooltip" title="{{ ucfirst(trans('textos.tareas')) }}" data-toggle="modal" data-target="#modal-member-tasks"><i class="fa fa-tasks"></i></a>

							{{-- Eliminar miembro --}}
							@if($tm->id != $project->owner)
								<a href="{{ route('projects.delete-member', [$project->id, $tm->id]) }}" class="btn btn-xs btn-danger btn-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.miembro_quitar')) }}" data-msg="{{ trans('textos.miembro_quitar_confirm') }}"><i class="fa fa-trash"></i></a>
							@else
								<span class="btn btn-xs" rel="tooltip" title="{{ trans('textos.propietario') }}">
									<i class="fa fa-bookmark text-warning"></i>	
								</span>	
							@endif
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
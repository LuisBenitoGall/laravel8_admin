@if($project)
	<label class="mr-5">
		{{ ucfirst(trans('textos.proyecto')) }}:	
		<b>
			<a href="{{ route('projects.edit', $project->id) }}" class="text-warning">
				@if($project->code) {{ $project->code }} @else {{ $project->id }} @endif
			</a>
		</b>
	</label>
@endif
<div class="row">
	@foreach($groups_in as $gi)
		<div class="col-6 col-md-3 mb-3">
			<h4>{{ $gi->title }}</h4>

			@php
			$projects_ = $gi->projects();
			@endphp

			<ul>
				@foreach($projects_ as $p)
					@php
					$project_num = $p->project_num? $p->project_num.' - ':($p->project_old_num? $p->project_old_num.' - ':'');
					@endphp
					<li class="py-1">
						<a target="_blank" href="{{ route('projects.edit', $p->id) }}">
							{{ $project_num }}{{ $p->name }}
						</a>
					</li>
				@endforeach
			</ul>
		</div>
	@endforeach
</div>

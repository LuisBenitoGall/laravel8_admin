{{-- Editar --}}	
@if($row->user_id == Auth::user()->id)
	<a href="{{ route('user-recommendations.edit', $row->id) }}" class="btn btn-xs btn-info btn-module" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}">
		<i class="fa fa-edit"></i>
	</a>
@endif

{{-- Ver --}}
<a target="_blank" href="{{ route('user-recommendations.show', $row->id) }}" class="btn btn-xs btn-info btn-module" rel="tooltip" title="{{ ucfirst(trans('textos.ver')) }}">
	<i class="fa fa-eye"></i>
</a>


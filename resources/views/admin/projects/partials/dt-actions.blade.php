@can('projects.edit')
	{{-- Estado --}}
	@if($manage_status)
		@include('admin.partials.selector-status', ['estados' => $estados, 'item' => $row])
	@endif

	{{-- Favoritos --}}
	<a href="#" class="btn btn-xs btn-info btnFav @if(in_array($row->id, $favourites)) selected @endif" rel="tooltip" title="{{ ucfirst(trans('textos.favorito')) }}" data-project="{{ $row->id }}"><i class="fa fa-star"></i></a>

    {{-- Editar --}}
	<a href="{{ route('projects.edit', $row) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>
@endcan

@can('projects.destroy')
	{{-- Eliminar --}}
	<form method="POST" action="{{ route('projects.destroy', $row->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
	    <input name="_method" type="hidden" value="DELETE">
	    {{ csrf_field() }}
	    <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.proyecto_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
	</form>
@endcan
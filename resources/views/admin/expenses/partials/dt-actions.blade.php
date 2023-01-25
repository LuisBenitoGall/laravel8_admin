@can('expenses.edit')
    {{-- Editar --}}
	<a target="_blank" href="{{ route('expenses.edit', $row) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>
@endcan

@if($return == 'expenses')
	@can('expenses.destroy')
		{{-- Eliminar --}}
		<form method="POST" action="{{ route('expenses.destroy', $row) }}" class="frm-inline" role="form" accept-charset="UTF-8">
		    <input name="_method" type="hidden" value="DELETE">
		    {{ csrf_field() }}
		    <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.gasto_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
		</form>
	@endcan

@elseif($return == 'projects')
	@can('expenses.edit')
		{{-- Desvincular gasto del proyecto --}}
		<form method="POST" action="{{ route('expenses.unlink-project', $row) }}" class="frm-inline" role="form" accept-charset="UTF-8">
		    {{ csrf_field() }}
		    <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.gasto_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
		</form>
	@endcan
@endif
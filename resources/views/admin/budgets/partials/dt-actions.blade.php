@can('budgets.edit')
	@if($row->provider_id == session('currentCompany'))
		{{-- Estado --}}
		@include('admin.partials.selector-status', ['estados' => $estados, 'item' => $row])
	@endif

	{{-- Ver --}}
	<a href="{{ route('budgets.show', $row) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.ver')) }}"><i class="fa fa-eye"></i></a>

    {{-- Editar. Los clientes pueden editar si han sido habilitados por el proveedor --}}
    @if($row->budget_shared || $row->provider_id == session('currentCompany'))
		<a href="{{ route('budgets.edit', $row) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>
	@endif
@endcan

@can('budgets.destroy')
	{{-- Eliminar --}}
	@if($row->provider_id == session('currentCompany'))
		<form method="POST" action="{{ route('budgets.destroy', $row->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
		    <input name="_method" type="hidden" value="DELETE">
		    {{ csrf_field() }}
		    <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.presupuesto_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
		</form>
	@endif
@endcan
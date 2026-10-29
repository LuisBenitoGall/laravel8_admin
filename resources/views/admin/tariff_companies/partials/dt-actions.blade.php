@can('customers.edit')
	{{-- Editar cliente --}}
	<a href="{{ route('customers.edit', $row->company_to) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>

	{{-- Eliminar cliente de la tarifa --}}
	<form method="POST" action="{{ route('tariff-companies.destroy', $row->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
		<input name="_method" type="hidden" value="DELETE">
		{{ csrf_field() }}
		<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{!! ucfirst(trans("textos.tarifa_retirar")) !!}" data-msg="{!! trans("textos.tarifa_retirar_confirm") !!}"><i class="fa fa-trash"></i></button>
	</form>
@endcan 

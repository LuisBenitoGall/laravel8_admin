@can('amortizations.edit')
{{-- Editar --}}
<a href="{{ route('amortizations.edit', $row) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>
@endcan

@can('amortizations.destroy')
{{-- Eliminar --}}
<form method="POST" action="amortizations/{{ $row->id }}" class="frm-inline" role="form" accept-charset="UTF-8">
<input name="_method" type="hidden" value="DELETE">
{{ csrf_field() }}
<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.amortizacion_eliminar')) }}" data-msg="{{ ucfirst(trans('textos.amortizacion_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
</form>
@endcan
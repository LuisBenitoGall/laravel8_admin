@can('amortizations.edit')
{{-- Editar --}}
<a href="#" class="btn btn-xs btn-info btnEditLine" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}" data-toggle="modal" data-target="#modal-edit-line" data-id="{{ $row->id }}" data-amount="{{ $row->amount }}" data-percent="{{ $row->percent }}"><i class="fa fa-edit"></i></a>

{{-- Eliminar --}}
<form method="POST" action="{{ route('amortization-lines.destroy', $row->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
<input name="_method" type="hidden" value="DELETE">
{{ csrf_field() }}
<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.amortizacion_linea_eliminar')) }}" data-msg="{{ ucfirst(trans('textos.amortizacion_linea_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
</form>
@endcan
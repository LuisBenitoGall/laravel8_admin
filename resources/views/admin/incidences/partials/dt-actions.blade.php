@can('incidences.edit')
    {{-- Editar: --}}
    <a href="{{ route('incidences.edit', $row->id) }}" class="btn btn-xs btn-info btn-module" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>
@endcan

@can('incidences.destroy')
    {{-- Eliminar: --}}
    <form method="POST" action="{{ route('incidences.destroy', $row->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
        <input name="_method" type="hidden" value="DELETE">
        {{ csrf_field() }}
        <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{!! ucfirst(trans("textos.incidencia_eliminar")) !!}" data-msg="{{ ucfirst(trans("textos.incidencia_eliminar_confirm")) }}"><i class="fa fa-trash"></i></button>
    </form>
@endcan

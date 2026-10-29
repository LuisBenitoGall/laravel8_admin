@can('contacts.show')
    {{-- Mostrar: --}}
    <a href="{{ route('contacts.show', $row->contact_id) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.ver')) }}"><i class="fa fa-eye"></i></a>
@endcan

@can('contacts.edit')
    {{-- Editar: --}}
    <a href="{{ route('contacts.edit', $row->contact_id) }}" class="btn btn-xs btn-info btn-module" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>
@endcan

@can('contacts.destroy')
    {{-- Eliminar: --}}
    <form method="POST" action="{{ route('contacts.destroy', $row->contact_id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
        <input name="_method" type="hidden" value="DELETE">
        {{ csrf_field() }}
        <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{!! ucfirst(trans("textos.contacto_eliminar")) !!}" data-msg="{{ ucfirst(trans("textos.contacto_eliminar_confirm")) }}"><i class="fa fa-trash"></i></button>
    </form>
@endcan
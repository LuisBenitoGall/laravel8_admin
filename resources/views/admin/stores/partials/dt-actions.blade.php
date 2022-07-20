@can('stores.edit')
    {{-- Estado --}}
    <label class="custom-chk-rdb" rel="tooltip" title="{{ trans('textos.almacen_on_off') }}">
        <input type="checkbox" name="status" @if($row->status == 1) checked @endif><span class="chk chk-action" data-id="{{ $row->id }}" data-ctrl="/admin/stores/status"></span>
    </label>

    {{-- Editar --}}
    <a href="{{ route('stores.edit', $row) }}" class="btn btn-xs btn-info btn-module" rel="tooltip" title="{{ ucfirst(trans('textos.almacen_editar')) }}"><i class="fa fa-edit"></i></a>
@endcan

@can('stores.destroy')
    {{-- Eliminar --}}
    <form method="POST" action="{{ route('stores.destroy', $row) }}" class="frm-inline" role="form" accept-charset="UTF-8">
        <input name="_method" type="hidden" value="DELETE">
        {{ csrf_field() }}
        <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.almacen_eliminar')) }}" data-msg="{{ trans('textos.almacen_confirm_eliminar') }}"><i class="fa fa-trash"></i></button>
    </form>
@endcan

               
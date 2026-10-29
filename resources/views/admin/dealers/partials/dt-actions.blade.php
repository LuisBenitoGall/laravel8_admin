@can('dealers.edit')
    {{-- Estado: --}}
    <label class="custom-chk-rdb" rel="tooltip" title="{{ trans('textos.repartidor_on_off') }}">
        <input type="checkbox" name="status" @if($row->status == 1) checked @endif><span class="chk chk-action" data-id="{{ $row->dealer_id }}" data-ctrl="dealers/status"></span>
    </label>
@endcan

{{-- Mostrar --}}
<a href="{{ route('dealers.show', $row->dealer_id) }}" class="btn btn-xs btn-info btn-module" rel="tooltip" title="{{ ucfirst(trans('textos.ver')) }}"><i class="fa fa-eye"></i></a>

@can('users.edit')
    {{-- Editar: --}}
    <a href="{{ route('users.edit', $row->id) }}" class="btn btn-xs btn-info btn-module" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>
@endcan

@can('dealers.destroy')
    {{-- Eliminar: --}}
    <form method="POST" action="{{ route('dealers.destroy', $row->dealer_id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
        <input name="_method" type="hidden" value="DELETE">
        {{ csrf_field() }}
        <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{!! ucfirst(trans("textos.repartidor_eliminar")) !!}" data-msg="{{ ucfirst(trans("textos.repartidor_confirm_eliminar")) }}"><i class="fa fa-trash"></i></button>
    </form>
@endcan

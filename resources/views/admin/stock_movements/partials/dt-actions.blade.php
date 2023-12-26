@can('stock-movements.edit')
    {{-- Estado --}}
    <label class="custom-chk-rdb" rel="tooltip" title="{{ trans('textos.movimiento_on_off') }}">
        <input type="checkbox" name="status" @if($row->status == 1) checked @endif><span class="chk chk-action" data-id="{{ $row->id }}" data-ctrl="/admin/stock-movements/status"></span>
    </label>

    {{-- Editar --}}
    <a href="{{ route('stock-movements.edit', $row) }}" class="btn btn-xs btn-info btn-module" rel="tooltip" title="{{ ucfirst(trans('textos.movimiento_editar')) }}"><i class="fa fa-edit"></i></a>
@endcan

@if(!$row->basic)
    @can('stock-movements.destroy')
        {{-- Eliminar --}}
        <form method="POST" action="{{ route('stock-movements.destroy', $row->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
            <input name="_method" type="hidden" value="DELETE">
            {{ csrf_field() }}
            <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.movimiento_eliminar')) }}" data-msg="{{ trans('textos.movimiento_confirm_eliminar') }}"><i class="fa fa-trash"></i></button>
        </form>
    @endcan
{{-- @else
    <span>{{ trans('textos.movimiento_basico') }}</span> --}}
@endif

               
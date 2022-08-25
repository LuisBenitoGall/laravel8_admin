@can('users.edit')
    {{-- Estado: --}}
    <label class="custom-chk-rdb" rel="tooltip" title="{{ trans('textos.usuario_on_off') }}">
        <input type="checkbox" name="status" @if($row->status == 1) checked @endif><span class="chk chk-action" data-id="{{ $row->id }}" data-ctrl="users/status"></span>
    </label>
@endcan

@can('users.show')
    {{-- Mostrar: --}}
    <a href="{{ route('users.show', $row->id) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.ver')) }}"><i class="fa fa-eye"></i></a>
@endcan

@can('companies.edit')
    {{-- Vincular empresas: --}}
    <a href="{{ route('user-companies.index', $row->id) }}" class="btn btn-xs btn-info" rel="tooltip" title="{!! ucfirst(trans("textos.empresa_vincular")) !!}"><i class="fa fa-building"></i></a>
@endcan

@can('users.edit')
    {{-- Editar: --}}
    <a href="{{ route('users.edit', $row->id) }}" class="btn btn-xs btn-info btn-module" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>
@endcan

{{-- Notificar: --}}
<a href="#" class="btn btn-warning btn-xs btn-notif" rel="tooltip" title="Notificar" data-toggle="modal" data-target="#modal-new-notification" data-id="{{ $row->id }}" data-user="{{ $row->name }} {{ $row->surname }}"><i class="fa fa-bell"></i></a>

@can('users.destroy')
    {{-- Eliminar: --}}
    <form method="POST" action="{{ route('users.destroy', $row->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
        <input name="_method" type="hidden" value="DELETE">
        {{ csrf_field() }}
        <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{!! ucfirst(trans("textos.usuario_eliminar")) !!}" data-msg="{{ ucfirst(trans("textos.usuario_confirm_eliminar")) }}"><i class="fa fa-trash"></i></button>
    </form>
@endcan

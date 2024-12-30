{{-- Leer --}}
<a href="{{ route('incidence-messages.show', $row->id) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.leer')) }}"><i class="fa fa-eye"></i></a>

{{-- Responder --}}
<a href="#" class="btn btn-xs btn-info btnAddResponse" data-toggle="modal" data-target="#modal-new-message" data-id="{{ $row->id }}" rel="tooltip" title="{{ ucfirst(trans('textos.responder')) }}"><i class="fa fa-reply"></i></a>

{{-- Eliminar --}}
<form method="POST" action="{{ route('incidence-messages.destroy', $row->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
    <input name="_method" type="hidden" value="DELETE">
    {{ csrf_field() }}
    <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.mensaje_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
</form>
@can('questions.edit')
	{{-- Estado --}}
	<label class="custom-chk-rdb" rel="tooltip" title="{{ trans('textos.cuestion_on_off') }}">
        <input type="checkbox" name="status" @if($row->status == 1) checked @endif><span class="chk chk-action" data-id="{{ $row->id }}" data-ctrl="/admin/questions/status"></span>
    </label>

    {{-- Editar --}}
	<a href="{{ route('questions.edit', $row) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>
@endcan

@can('questions.destroy')
	{{-- Eliminar --}}
	<form method="POST" action="{{ route('questions.destroy', $row->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
	    <input name="_method" type="hidden" value="DELETE">
	    {{ csrf_field() }}
	    <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.cuestion_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
	</form>
@endcan
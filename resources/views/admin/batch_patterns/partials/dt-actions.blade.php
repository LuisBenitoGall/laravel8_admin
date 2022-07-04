@can('batch-patterns.edit')
	{{-- Estado --}}
	<label class="custom-chk-rdb" rel="tooltip" title="{{ trans('textos.patron_on_off') }}">
        <input type="checkbox" name="status" @if($pattern->status == 1) checked @endif><span class="chk chk-action" data-id="{{ $pattern->id }}" data-ctrl="batch-patterns/status"></span>
    </label>

    {{-- Editar --}}
	<a href="{{ route('batch-patterns.edit', $pattern) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>
@endcan

@can('batch-patterns.destroy')
	{{-- Eliminar --}}
	<form method="POST" action="batch-patterns/{{ $pattern->id }}" class="frm-inline" role="form" accept-charset="UTF-8">
	    <input name="_method" type="hidden" value="DELETE">
	    {{ csrf_field() }}
	    <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.lotes_patron_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
	</form>
@endcan
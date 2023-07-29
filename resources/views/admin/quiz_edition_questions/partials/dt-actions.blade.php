{{-- Estado - condiciones:
    1. Si el concurso no está activo.
 --}}
@if($disabled != 'disabled')
    <label class="custom-chk-rdb" rel="tooltip" title="{{ trans('textos.cuestion_on_off') }}">
        <input type="checkbox" name="status" @if($row->qeq_id) checked @endif><span class="chk chkSetQuestion" data-id="{{ $row->id }}"></span>
    </label>
@endif

@can('questions.edit')
    {{-- Editar --}}
    <a href="{{ route('questions.edit', $row) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>
@endcan
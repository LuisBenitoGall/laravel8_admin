{{-- Estado: --}}
<label class="custom-chk-rdb" rel="tooltip" title="{{ trans('textos.empresa_on_off') }}">
    <input type="checkbox" name="status" @if($row->status == 1) checked @endif><span class="chk chk-action" data-id="{{ $row->id }}" data-ctrl="/admin/quiz-edition-companies/status"></span>
</label>

{{-- Eliminar: --}}
<form method="POST" action="{{ route('quiz-edition-companies.destroy', $row->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
    <input name="_method" type="hidden" value="DELETE">
    {{ csrf_field() }}
    <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{!! ucfirst(trans("textos.empresa_desvincular")) !!}" data-msg="{{ ucfirst(trans("textos.empresa_desvincular_confirm")) }}"><i class="fa fa-trash"></i></button>
</form>

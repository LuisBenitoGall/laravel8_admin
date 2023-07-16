{{-- Eliminar: --}}
<form method="POST" action="{{ route('quiz-edition-teams.destroy', $row->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
    <input name="_method" type="hidden" value="DELETE">
    {{ csrf_field() }}
    <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{!! ucfirst(trans("textos.equipo_desvincular")) !!}" data-msg="{{ ucfirst(trans("textos.equipo_confirm_desvincular")) }}"><i class="fa fa-trash"></i></button>
</form>
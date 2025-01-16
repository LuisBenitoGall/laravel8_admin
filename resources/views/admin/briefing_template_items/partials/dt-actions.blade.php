@can('briefing-templates.edit')
	{{-- Traducciones --}}
    <a href="#" class="btn btn-xs btn-info btn-translations" rel="tooltip" title="{{ ucfirst(trans('textos.traducciones')) }}" data-toggle="modal" data-target="#modal-translations" data-id="{{ $row->id }}" data-schema="briefing_template_items"><i class="fa fa-globe"></i></a>

	{{-- Editar --}}
	<a href="{{ route('briefing-template-items.edit', $row) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>

	{{-- Eliminar --}}
	<form method="POST" action="{{ route('briefing-template-items.destroy', $row) }}" class="frm-inline" role="form" accept-charset="UTF-8">
	    <input name="_method" type="hidden" value="DELETE">
	    {{ csrf_field() }}
	    <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.item_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
	</form>
@endcan
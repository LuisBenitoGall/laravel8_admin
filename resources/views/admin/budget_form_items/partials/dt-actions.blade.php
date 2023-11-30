{{-- Traducciones --}}
<a href="#" class="btn btn-xs btn-info btn-translations" rel="tooltip" title="{{ ucfirst(trans('textos.traducciones')) }}" data-toggle="modal" data-target="#modal-translations" data-id="{{ $row->id }}" data-schema="budget_form_items"><i class="fa fa-globe"></i></a>

{{-- Editar --}}
@can('budget-forms.edit')
    <a href="#" class="btn btn-xs btn-info btn-module btnEditFormBlock" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}" data-toggle="modal" data-target="#modal-edit-block" data-name="{{ $row->name }}" data-costcenter="{{ $row->cost_center_id }}" data-id="{{ $row->id }}"><i class="fa fa-edit"></i></a>
@endcan

{{-- Eliminar --}}
@can('budget-forms.edit')   
    <form method="POST" action="{{ route('budget-form-items.destroy', $row->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
        <input name="_method" type="hidden" value="DELETE">
        {{ csrf_field() }}
        <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{!! ucfirst(trans('textos.bloque_eliminar')) !!}" data-msg="{!! ucfirst(trans('textos.bloque_confirm_eliminar')) !!}"><i class="fa fa-trash"></i></button>
    </form>
@endcan
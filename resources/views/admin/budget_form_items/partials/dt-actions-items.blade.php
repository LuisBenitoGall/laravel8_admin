{{-- Fijo en los presupuestos: --}}
@can('budget-forms.edit')
    @php
    $checked = $row->fixed_in_budget == 1? 'checked':'';
    @endphp
    <label class="custom-chk-rdb" rel="tooltip" title="{{ trans('textos.item_fijo_presupuesto') }}">
        <input type="checkbox" name="status" {{ $checked }} ><span class="chk chk-action" data-id="{{ $row->id }}" data-ctrl="/admin/budget-form-items/fixed"></span>
    </label>
@endcan

{{-- Traducciones --}}
<a href="#" class="btn btn-xs btn-info btn-translations" rel="tooltip" title="{{ ucfirst(trans('textos.traducciones')) }}" data-toggle="modal" data-target="#modal-translations" data-id="{{ $row->id }}" data-schema="budget_form_items"><i class="fa fa-globe"></i></a>

{{-- Editar item --}}
@can('budget-forms.edit')
    <a href="#" class="btn btn-xs btn-info btn-module btnEditFormItem" rel="tooltip" title="{{ ucfirst(trans('textos.item_editar')) }}" data-toggle="modal" data-target="#modal-edit-item" data-name="{{ $row->name }}" data-id="{{ $row->id }}"><i class="fa fa-edit"></i></a>
@endcan

{{-- Editar producto --}}
@can('products.edit')
    @if($row->product_id)
        <a target="_blank" href="{{ route('products.edit', $row->product_id) }}" class="btn btn-xs btn-warning" rel="tooltip" title="{{ ucfirst(trans('textos.producto_editar')) }}"><i class="fa fa-edit"></i></a>
    @endif
@endcan

{{-- Eliminar --}}
@can('budget-forms.edit')   
    <form method="POST" action="{{ route('budget-form-items.destroy', $row->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
        <input name="_method" type="hidden" value="DELETE">
        {{ csrf_field() }}
        <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{!! ucfirst(trans('textos.item_eliminar')) !!}" data-msg="{!! ucfirst(trans('textos.item_confirm_eliminar')) !!}"><i class="fa fa-trash"></i></button>
    </form>
@endcan
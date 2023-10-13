@can('products.edit')
	{{-- Asignar a usuario - condiciones:
		1. No asignado a ningún usuario.
	--}}
	@if(!$user)
		<a href="#" class="btn btn-xs btn-info btnAssignMaterial" data-serial="{{ $row->id }}" data-toggle="modal" data-target="#modal-assign-material" rel="tooltip" title="{{ trans('textos.asignar_a_empleado') }}">
			<i class="fa fa-hands"></i>	
		</a>
	@endif

	{{-- Ficha de la unidad --}}
	<a target="_blank" href="{{ route('product-serials-cards.edit', $row->id) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ trans('textos.ficha_unidad') }}">
		<i class="fa fa-file-alt"></i>
	</a>

	{{-- Eliminar --}}
    <form method="POST" action="{{ route('product-serials.destroy', $row->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
        <input name="_method" type="hidden" value="DELETE">
        {{ csrf_field() }}
        <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{!! ucfirst(trans('textos.serial_eliminar')) !!}" data-msg="{!! ucfirst(trans('textos.serial_confirm_eliminar')) !!}"><i class="fa fa-trash"></i></button>
    </form>
@endcan
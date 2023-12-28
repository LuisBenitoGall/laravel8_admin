@can('effects.edit')
	{{-- Estado --}}
	@include('admin.partials.selector-status', ['estados' => $estados, 'item' => $row])

	{{-- Editar --}}
	<a href="{{ route('effects.edit', $row) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>

	{{-- Remesar --}}
	@if(!$row->remittance_id)
		<a href="#" class="btn btn-xs btn-info btnAddRemittance" data-toggle="modal" data-target="#modal-add-remittance" data-id="{{ $row->id }}" rel="tooltip" title="{{ ucfirst(trans('textos.remesar')) }}"><i class="fa fa-layer-group"></i></a>
	@endif
@endcan

@can('effects.destroy')
	{{-- Eliminar: sólo si el estado es de pendiente --}}
	@if($row->status == config('constants.EFFECT_STATUS_PENDENT_'))
		<form method="POST" action="{{ route('effects.destroy', $row) }}" class="frm-inline" role="form" accept-charset="UTF-8">
		<input name="_method" type="hidden" value="DELETE">
		{{ csrf_field() }}
		<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.efecto_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
		</form>
	@endif
@endcan

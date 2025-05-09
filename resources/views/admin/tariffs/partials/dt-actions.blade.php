@can('tariffs.edit')
	{{-- Estado: --}}
	<label class="custom-chk-rdb" rel="tooltip" title="{{ trans('textos.tarifa_on_off') }}">
		<input type="checkbox" name="status" @if($row->status == 1) checked @endif><span class="chk chk-action" data-id="{{ $row->id }}" data-ctrl="tariffs/status"></span>
	</label>

	{{-- Editar --}}
	<a href="{{ route('tariffs.edit', $row->id) }}" class="btn btn-xs btn-info btn-module" rel="tooltip" title="{{ ucfirst(trans('textos.tarifa_edit')) }}"><i class="fa fa-edit"></i></a>

	{{-- Clientes --}}
	<a href="{{ route('tariff-companies.index', $row->id) }}" class="btn btn-xs btn-info btn-module" rel="tooltip" title="{{ ucfirst(trans('textos.tarifa_clientes')) }}"><i class="fa fa-building"></i></a>

	{{-- Obras --}}
	{{-- <a href="{{ route('tariff-obras.index', $row->id) }}" class="btn btn-xs btn-info btn-module" rel="tooltip" title="{{ ucfirst(trans('textos.tarifa_obras')) }}"><i class="fa fa-warehouse"></i></a> --}}

	{{-- Tarifa por defecto --}}
	@if($row->featured)
		<a href="{{ route('tariffs.remove-featured', $row->id) }}" class="btn btn-xs btn-tte btn-confirm" data-msg="Confirma que deseas cancelar la tarifa por defecto">
			<i class="fa fa-star text-warning" rel="tooltip" title="{{ trans('textos.tarifa_principal') }}"></i>
		</a>

	@elseif(!$row->featured && $row->status == 1)
		<a href="{{ route('tariffs.featured', $row->id) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ trans('textos.tarifa_principal_marcar') }}"><i class="fa fa-star"></i></a>
	@endif
@endcan

@can('tariffs.destroy')
	<form method="POST" action="{{ route('tariffs.destroy', $row->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
		<input name="_method" type="hidden" value="DELETE">
		{{ csrf_field() }}
		<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.tarifa_eliminar_confirm')) }}"><i class="fa fa-trash"></i></button>
	</form>
@endcan
@can('companies.edit')
	{{-- Estado --}}
	<label class="custom-chk-rdb" rel="tooltip" title="{{ ucfirst(trans('textos.empresa_on_off')) }}">
        <input type="checkbox" name="status" @if($row->status) checked @endif><span class="chk chk-action" data-id="{{ $row->id }}" data-ctrl="companies/status"></span>
    </label>

    {{-- Traspaso de empresa --}}
    <a href="#" class="btn btn-xs btn-info btn-module btnTransferCompany" rel="tooltip" title="{{ ucfirst(trans('textos.empresa_traspaso')) }}" data-toggle="modal" data-target="#modal-transfer-company" data-id="{{ $row->id }}"><i class="fa fa-handshake"></i></a>

	{{-- Editar --}}
	<a target="_blank" href="{{ route('companies.edit', $row) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>
@endcan

@can('companies.destroy')
	{{-- Eliminar --}}
	<form method="POST" action="companies/{{ $row->id }}" class="frm-inline" role="form" accept-charset="UTF-8">
        <input name="_method" type="hidden" value="DELETE">
        {{ csrf_field() }}
        <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.empresa_eliminar')) }}" data-msg="{{ ucfirst(trans('textos.empresa_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
    </form>
@endcan

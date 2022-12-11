@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Nuevo tipo --}}
		@can('work-calendar.edit')
			<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-add-type">
				<i class="fa fa-plus"></i>
				{!! ucfirst(trans('textos.tipo_nuevo')) !!}
			</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dTable" id="tblIvaTypes">
			<thead>
				<tr>
					<th>{{ ucfirst(trans('textos.tipo')) }}</th>
					<th class="text-center">IVA</th>
					<th class="text-center">{{ ucfirst(trans('textos.recargo_equivalencia')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.acciones')) }}</th>
				</tr>
			</thead>

			<tbody>
				@foreach($types as $t)
					<tr>
						<td>{{ $t->name }}</td>
						<td class="text-right">{{ $t->iva }}%</td>
						<td class="text-right">{{ $t->equivalence_surcharge }}%</td>
						{{-- Acciones --}}
						<td class="text-right">
							@can('iva-types.edit')
								{{-- Estado --}}
								<label class="custom-chk-rdb" rel="tooltip" title="{{ trans('textos.tipo_on_off') }}">
							        <input type="checkbox" name="status" @if($t->status == 1) checked @endif><span class="chk chk-action" data-id="{{ $t->id }}" data-ctrl="iva-types/status"></span>
							    </label>

								{{-- Editar --}}
								<a href="#" class="btn btn-xs btn-info btnEdit" data-toggle="modal" data-target="#modal-edit-type" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}" data-id="{{ $t->id }}" data-name="{{ $t->name }}" data-iva="{{ $t->iva }}" data-es="{{ $t->equivalence_surcharge }}">
									<i class="fa fa-edit"></i>
								</a>
							@endcan

							@can('iva-types.destroy')
								{{-- Eliminar --}}
								<form method="POST" action="{{ route('iva-types.destroy', $t->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
								    <input name="_method" type="hidden" value="DELETE">
								    {{ csrf_field() }}
								    <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ ucfirst(trans('textos.tipo_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
								</form>
							@endcan
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>

{{-- Modals: --}}
{{-- Add type --}}
@include('admin.iva_types.partials.modal-add-type')

{{-- Edit type --}}
@include('admin.iva_types.partials.modal-edit-type')

@push('scripts')
<script>
$(function(){
	//Modal edición tipo de IVA:
	$(document).on('click', '.btnEdit', function(){
		var id = $(this).data('id');
		var name = $(this).data('name');
		var iva = $(this).data('iva');
		var es = $(this).data('es');

		$('#typeId').val(id);
		$('#typeName').val(name);
		$('#typeIva').val(iva);
		$('#typeEs').val(es);
	});
});
</script>
@endpush


@endsection
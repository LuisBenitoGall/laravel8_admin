@php
$functionalities = App\Models\Functionality::where('module_id', $module->id)->get();
@endphp

<div class="table-responsive">
	<table class="table table-hover dTable" id="tblFunctionalities">
		<thead>
			<tr>
				<th>{!! ucfirst(trans('textos.etiqueta')) !!}</th>
				<th>{!! ucfirst(trans('textos.funcionalidad')) !!}</th>
				<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
			</tr>
		</thead>

		<tbody>
			@foreach($functionalities as $row)	
				<tr>
					<td>{{ $row->label }}</td>
					<td>{{ $row->name }}</td>
					{{-- Acciones --}}
					<td class="text-right">
						{{-- Editar --}}
						<a href="#" class="btn btn-xs btn-info btnFunctionality" data-toggle="modal" data-target="#modal-edit-functionality" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}" data-id="{{ $row->id }}" data-label="{{ $row->label }}"><i class="fa fa-edit"></i></a>	

						{{-- Eliminar --}}
						<form method="POST" action="{{ route('functionalities.destroy', $row->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
	                        <input name="_method" type="hidden" value="DELETE">
	                        {{ csrf_field() }}
	                        <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ trans('textos.funcionalidad_confirm_eliminar') }}"><i class="fa fa-trash"></i></button>
	                    </form>
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
</div> 
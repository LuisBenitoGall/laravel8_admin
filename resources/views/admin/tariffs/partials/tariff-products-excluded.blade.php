<div class="table-responsive">
	<table class="table table-hover dTable" id="tblTariffProductsExcluded">
		<thead>
			<tr>
				<th>{{ ucfirst(trans('textos.referencia')) }}</th>
				<th>{{ ucfirst(trans('textos.producto')) }}</th>
				<th class="text-center">{{ ucfirst(trans('textos.margen')) }}</th>
				<th class="text-center">{{ ucfirst(trans('textos.acciones')) }}</th>
			</tr>
		</thead>

		<tbody>
			@foreach($products_excluded as $pe)
				<tr>
					{{-- Referencia --}}
					<td>
						@php
						$ref = $pe->ref? $pe->ref:($pe->manual_ref? $pe->manual_ref:'');
						@endphp
						{{ $ref }}
					</td>
					{{-- Producto --}}
					<td>{{ $pe->name }}</td>
					{{-- Margen beneficio --}}
					<td class="text-right">{{ $pe->discount }}%</td>
					{{-- Acciones --}}
					<td class="text-right">
						{{-- Editar descuento --}}
						<a href="#" class="btn btn-xs btn-success btnEdit" rel="tooltip" title="{{ trans('textos.producto_premium_edit') }}" data-toggle="modal" data-target="#modal-edit-product" data-pe="{{ $pe->pe_id }}" data-discount="{{ $pe->discount }}" data-product="{{ $pe->name }}">
							<i class="fa fa-edit"></i>
						</a>

						{{-- Editar producto --}}
						<a href="{{ route('products.edit', $pe->id) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.producto_editar')) }}">
							<i class="fa fa-edit"></i>
						</a>

						{{-- Eliminar producto excluido --}}
						<a href="{{ route('tariff-products-excluded.delete', $pe->pe_id) }}" class="btn btn-xs btn-danger btn-confirm" data-msg="Confirma que deseas retirar este producto Premium" rel="tooltip" title="{{ ucfirst(trans('textos.producto_eliminar')) }}">
							<i class="fa fa-trash"></i>
						</a>
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
</div>
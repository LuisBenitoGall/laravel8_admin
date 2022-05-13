<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dTable" id="tblProjectPurchases">
			<thead>
				<tr>
					<th>{{ ucfirst(trans('textos.proyecto')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.acciones')) }}</th>
				</tr>
			</thead>

			<tbody>
				@foreach($project_purchases as $pp)
					<tr>
						<td>{{ $pp->name }}</td>
						<td class="text-right">
							@can('projects.edit')
								{{-- Editar --}}
								<a target="_blank" href="{{ route('projects.edit', $pp->projectId) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>

								{{-- Desvincular --}}
								<form method="POST" action="{{ route('purchase-from-project.destroy', $pp->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
	                            	<input name="_method" type="hidden" value="DELETE">
	                            	{{ csrf_field() }}
	                            	<input type="hidden" name="return" value="order">
	                            	<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.desvincular')) }}" data-msg="{!! ucfirst(trans("textos.compra_proyecto_desvincular_confirm")) !!}"><i class="fa fa-trash"></i></button>
	                        	</form>
							@endcan
						</td>
					</tr>
				@endforeach	
			</tbody>
		</table>
	</div>
</div>
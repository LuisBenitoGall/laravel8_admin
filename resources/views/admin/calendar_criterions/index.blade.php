@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Nuevo criterio --}}
		@can('work-calendar.edit')
			<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-add-criterion">
				<i class="fa fa-plus"></i>
				{!! ucfirst(trans('textos.criterio_nuevo')) !!}
			</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dTable" id="tblCalendarCriterions">
			<thead>
				<tr>
					<th>{{ ucfirst(trans('textos.criterio')) }}</th>
					<th>{{ ucfirst(trans('textos.descripcion')) }}</th>
					<th>{{ ucfirst(trans('textos.metodo')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.acciones')) }}</th>
				</tr>
			</thead>

			<tbody>
				@foreach($criterions as $row)
					<tr>
						<td>{{ $row->name }}</td>
						<td>{!! $row->description !!}</td>
						<td>{{ $row->method }}</td>
						<td class="text-right">
							{{-- Editar --}}
							<a href="#" class="btn btn-xs btn-info btnEdit" data-toggle="modal" data-target="#modal-edit-criterion" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}" data-id="{{ $row->id }}" data-name="{{ $row->name }}" data-description="{{ $row->description }}">
								<i class="fa fa-edit"></i>
							</a>
						</td>
					</tr>
				@endforeach		
			</tbody>
		</table>
	</div>
</div>

{{-- Modals: --}}
{{-- Add criterion --}}
@include('admin.calendar_criterions.partials.modal-add-criterion')

{{-- Edit criterion --}}
@include('admin.calendar_criterions.partials.modal-edit-criterion')

@push('scripts')
<script>
$(function(){
	//Modal edición criterio:
	$(document).on('click', '.btnEdit', function(){
		var id = $(this).data('id');
		var name = $(this).data('name');
		var description = $(this).data('description');

		$('#criterionId').val(id);
		$('#criterionName').val(name);
		$('#criterionDescription').trumbowyg("html", description);
	});
});
</script>
@endpush

@endsection
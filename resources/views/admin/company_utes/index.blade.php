@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Crear nueva UTE --}}
		@can('companies.create')
			<a class="btn btn-info" href="{{ route('companies.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.ute_nueva')) !!}</a>
		@endcan

		{{-- Vincular empresa a UTE --}}
		@can('companies.edit')
			<a class="btn btn-info" href="#" data-toggle="modal" data-target="#modal-add-company"><i class="fa fa-building"></i> {!! ucfirst(trans('textos.ute_empresa_vincular')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12 mb-3">
		<h3>UTE <b>{{ $ute->name }}</b></h3>
	</div>

	<div class="table-responsive">
		<table class="table table-hover dTable" id="tblUtes">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.razon_social')) !!}</th>
					<th>{!! ucfirst(trans('textos.nombre_comercial')) !!}</th>
					<th>{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
					<th>NIF</th>
					<th class="text-center">{!! ucfirst(trans('textos.participacion')) !!}</th>
					<th>Logo</th>
					<th>{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>

			<tbody>
				@php
				$participated = 0;
				@endphp

				@foreach($ute_companies as $row)
					@php
					$participated = $participated + $row->participation_percent;
					@endphp

					<tr>
						{{-- Razón social --}}
						<td>{{ $row->name }}</td>
						{{-- Nombre comercial --}}
						<td>{{ $row->tradename }}</td>
						{{-- Fecha alta --}}
						<td class="text-right">
							
						</td>
						{{-- NIF --}}
						<td>{{ $row->nif }}</td>
						{{-- Participación --}}
						<td class="text-right">{{ $row->participation_percent }}%</td>
						{{-- Logo --}}
						<td class="text-center">
							
						</td>
						{{-- Acciones --}}
						<td class="text-right">
							{{-- Editar empresa --}}
							<a target="_blank" href="{{ route('companies.edit', $row) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.empresa_editar')) }}"><i class="fa fa-edit"></i></a>

							{{-- Editar participación --}}
							<a href="#" class="btn btn-xs btn-info btnEditParticipation" rel="tooltip" title="{{ ucfirst(trans('textos.participacion_editar')) }}" data-id="{{ $row->company_ute_id }}" data-participation="{{ $row->participation_percent }}" data-toggle="modal" data-target="#modal-edit-participation"><i class="fa fa-percent"></i></a>

							{{-- Desvincular de UTE --}}
							<form method="POST" action="{{ route('company-utes.destroy', $row->company_ute_id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
						        <input name="_method" type="hidden" value="DELETE">
						        {{ csrf_field() }}
						        <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.empresa_desvincular')) }}" data-msg="{{ ucfirst(trans('textos.ute_empresa_confirm_desvincular')) }}"><i class="fa fa-trash"></i></button>
						    </form>
						</td>
					</tr>
				@endforeach
			</tbody>	

			<tfoot>
				<tr>
					<td colspan="4" class="text-right">
						<b>{{ trans('textos.participado_total') }}</b>
					</td>
					<td class="text-right">
						{{ $participated }}%	
					</td>
					<td></td>
					<td></td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>

{{-- Modals --}}
{{-- Add company --}}
@include('admin.company_utes.partials.modal-add-company')

{{-- Edit participation percent --}}
@include('admin.company_utes.partials.modal-edit-participation')

@push('scripts')
<script>
$(function(){
	//Modal editar participación:
	$(document).on('click', '.btnEditParticipation', function(){
		var id = $(this).data('id');
		var participation = $(this).data('participation');

		$('#uteId').val(id);
		$('#uteParticipation').val(participation);
	});

});
</script>
@endpush

@endsection
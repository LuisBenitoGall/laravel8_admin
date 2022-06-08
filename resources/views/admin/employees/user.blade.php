@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones --}}
	<div class="col-12">
		<a href="{{ route('users.edit', $user->id) }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_editar_usuario') !!}
		</a>

		@can('employees.create')
			<a class="btn btn-info" href="{{ route('employees.create', $user->id) }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.contrato_nuevo')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12 mb-3">						
		<h3><u>{!! trans('textos.contratos_de') !!} {{ $user->name }} {{ $user->surname }}</u></h3>	
	</div>

	<div class="table-responsive">
		<table class="table table-hover dTable" id="tblUserContracts">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.contrato')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.inicio')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fin')) !!}</th>
					<th>{!! ucfirst(trans('textos.puesto')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.horas_semanales')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.contrato_estado')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>

			<tbody>
				@foreach($employees as $row)
					<tr>
						<td>{{ $row->Contract }}</td>
						<td class="text-right">
							@php
							$inicio = Carbon\Carbon::parse($row->start_date)->format($languages[session()->get('locale')][4]);
							@endphp
							{{ $inicio }}
						</td>
						<td class="text-right">
							@if($row->end_date == config('constants.UNDEFINED_DATE_'))
								@php
								$fin = ucfirst(trans('textos.indefinido'));
								@endphp
							@else
								@php
								$fin = Carbon\Carbon::parse($row->end_date)->format($languages[session()->get('locale')][4]);
								@endphp
							@endif
							{{ $fin }}
						</td>
						<td>{{ $row->job_title }}</td>
						<td class="text-right">{{ $row->weekly_hours }}</td>
						<td class="text-center">
							@if($row->status == 0)
								<i class="fa fa-exclamation text-warning" rel="tooltip" title="{{ ucfirst(trans('textos.contrato_inactivo')) }}"></i>
							@elseif($row->status == 1)
								<i class="fa fa-check text-success" rel="tooltip" title="{{ ucfirst(trans('textos.contrato_activo')) }}"></i>
							@else
								<i class="fa fa-ban text-danger" rel="tooltip" title="{{ ucfirst(trans('textos.contrato_vencido')) }}"></i>	
							@endif
						</td>
						<td class="text-right">
							{{-- Editar: --}}
							@can('employees.edit')
	                            <a href="{{ route('employees.edit', $row->id) }}" class="btn btn-xs btn-info" rel="tooltip" title="{!! ucfirst(trans('textos.contrato_editar')) !!}">
	                                <i class="fa fa-edit"></i>
	                            </a>
	                        @endcan	
	                        
	                        @if($row->status == 1)
	                        	{{-- Horarios personalizados: --}}
	                       		@can('employees.edit')
		                       		<a href="#" class="btn btn-xs btn-info btnCustomTimetables" rel="tooltip" title="{!! ucfirst(trans('textos.horarios_personalizados')) !!}" data-toggle="modal" data-target="#modal-custom_timetables" data-id="{{ $row->id }}" data-companyid="{{ $row->companyId }}">
		                                <i class="fa fa-clock"></i>
		                            </a>
	                        	@endcan	

	                        	{{-- Cancelar contrato: --}}
								@can('employees.edit')
									<a href="#" class="btn btn-xs btn-warning btnCancelEmployee" rel="tooltip" title="{!! ucfirst(trans('textos.contrato_cancelar')) !!}" data-toggle="modal" data-target="#modal-cancel-employee" data-id="{{ $row->id }}">
		                            	<i class="fa fa-ban"></i>
		                        	</a>
								@endcan

		                        {{-- Eliminar: --}}
		                        @can('employees.destroy')
									<form method="POST" action="{{ route('employees.destroy', $row->id) }}" class="frm-inline" role="form" accept-charset="UTF-8"> 
										<input name="_method" type="hidden" value="DELETE">
										{{ csrf_field() }}

		                                <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{!! ucfirst(trans('textos.contrato_eliminar')) !!}" data-msg="{{ ucfirst(trans('textos.contrato_confirm_eliminar')) }}">
		                                	<i class="fa fa-trash"></i>
		                                </button>
		                            </form>
								@endcan
							@endif
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>

{{-- Modals: --}}
{{-- Cancel employee: --}}
<div class="modal fade opened-modal-save" id="modal-cancel-employee" tabindex="-1" role="dialog" aria-labelledby="cancelEmployee" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="cancelEmployee">{!! ucfirst(trans('textos.contrato_cancelar')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('employees.cancel') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
					<input type="hidden" name="user_id" value="{{ $user->id }}">
					<input type="hidden" name="id" id="employeeIdCancel">

					{{-- Fecha cancelación --}}
					<div class="form-group row">
                        <label for="end" class="col-lg-12">{!! ucfirst(trans('textos.fecha')) !!}</label>
                        <div class="col-lg-12">
                        	<x-date-picker name="end" :format="$languages[config('app.locale')][4]" required/>
                        </div>
                    </div>

                    {{-- Observaciones --}}
                    <div class="form-group row">
                        <label for="observations" class="col-lg-12">{!! ucfirst(trans('textos.observaciones')) !!}</label>
                        <div class="col-lg-12">
                        	<x-input-textarea name="observations"/>
                        </div>
                    </div>

                    <x-btn-submit name="submit_cancel_contract" label="{{ trans('textos.enviar') }}"/>
                </form>    
            </div>
        </div>
    </div>
</div>

@endsection
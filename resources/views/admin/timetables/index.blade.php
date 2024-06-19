@extends('admin.partials.layout')

@section('content')

{{-- Filtro formulario: --}}
<div class="row mb-3">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('employees-timetable.index') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Centro de trabajo --}}
						@if($workplaces->count() > 1)
							<label for="workplace_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.centros_trabajo')) !!}*</label>
							<div class="col-lg-4">
								@php
								$workplaces_ = [];
								foreach($workplaces as $wp){
									$name = $wp->Town? $wp->name.' - '.$wp->Town:$wp->name;
									$workplaces_[$wp->id] = $name;
								}

								$value = $workplace? $workplace->id:'';
								@endphp

								<x-select name="workplace_id" class="select2" label="centro_trabajo_selec" :options="$workplaces_" value="{{ $value }}" required/>
							</div>
						@elseif($workplaces->count() == 1)
							<input type="hidden" name="workplace_id" value="{{ $workplaces[0]->id }}">
						@endif

						{{-- Grupo --}}
						<label for="group" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.grupo')) !!}</label>
						<div class="col-lg-4">
							<x-select name="group" class="select2" label="grupo_selecciona" :options="$groups" value="{{ $group }}"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Filtro empleados --}}
						<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.empleados')) !!}</label>
						<div class="col-lg-2">
						    {{-- Activos --}}
						    <label class="custom-chk-rdb mr-4" style="margin-bottom: 0; margin-top: 7px;">
					        	<input type="radio" name="status" value="1" @if($status == 1) checked @endif required>
					        	<span class="rdb chksmall"></span>
					        	{{ ucfirst(trans('textos.activos')) }}
						    </label> 

						    {{-- Todos --}}
							<label class="custom-chk-rdb mr-4" style="margin-bottom: 0; margin-top: 7px;">
					        	<input type="radio" name="status" value="x" @if($status == 'x') checked @endif required>
					        	<span class="rdb chksmall"></span>
					        	{{ trans('textos.todos') }}
						    </label>  
						</div>
					
						{{-- Desde --}}
						<label for="from" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.desde')) !!}</label>
						<div class="col-lg-2">
							@php
							$from_ = $from != ''? \Carbon\Carbon::parse($from)->format($languages[session()->get('locale')][4]):'';
							@endphp

							<x-date-picker name="from" value="{{ $from_ }}" :format="$languages[config('app.locale')][4]"/>	
						</div>

						{{-- Hasta --}}
						<label for="to" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.hasta')) !!}</label>
						<div class="col-lg-2">
							@php
							$to_ = $to != ''? \Carbon\Carbon::parse($to)->format($languages[session()->get('locale')][4]):'';
							@endphp

							<x-date-picker name="to" value="{{ $to_ }}" :format="$languages[config('app.locale')][4]"/>	
						</div>
					</div>

					<x-btn-submit name="submit_filter" class="ml-5" label="{{ trans('textos.buscar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

{{-- Resultados --}}
@if($workplace)	
	<input type="hidden" id="dtWorkplace" value="{{ $workplace->id }}">
	<input type="hidden" id="dtFrom" value="{{ $from }}">
	<input type="hidden" id="dtTo" value="{{ $to }}">
	<input type="hidden" id="dtStatus" value="{{ $status }}">
	<input type="hidden" id="dtGroup" value="{{ $group }}">

	<div class="row">
		<div class="table-responsive">
			<table class="table table-hover dtabla" id="tblEmployeesHours">
				<thead>
					<tr>
						<th>{!! ucfirst(trans('textos.empleado')) !!}</th>
						<th class="text-center">{!! ucfirst(trans('textos.horas_trabajadas')) !!}</th>
						<th class="text-center">{!! ucfirst(trans('textos.horas_laborables')) !!}</th>
						<th class="text-center">{!! ucfirst(trans('textos.saldo')) !!}</th>
						<th class="text-center">{!! ucfirst(trans('textos.situacion')) !!}</th>
						<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
					</tr>
				</thead>
			</table>
		</div>	
	</div>
@endif

@push('scripts')
<script>
$(function(){
	//Tabla Employees Hours:
    if($('#tblEmployeesHours').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var workplace_id = $('#dtWorkplace').val();
        var from = $('#dtFrom').val();
        var to = $('#dtTo').val();
        var status = $('#dtStatus').val();
        var group = $('#dtGroup').val();

        var tblEmployeesHours = $('#tblEmployeesHours').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/employees-timetable/"+workplace_id+"/"+from+"/"+to+"/"+status+"/"+group,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'employee'},
                {data: 'worked_hours', className: 'text-right'},
                {data: 'working_hours', className: 'text-right'},
                {data: 'difference', className: 'text-right'},
                {data: 'situation', className: 'text-center'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }
});
</script>
@endpush

@endsection
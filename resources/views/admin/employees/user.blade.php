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
		                       		<a href="#" class="btn btn-xs btn-info btnCustomTimetables" rel="tooltip" title="{!! ucfirst(trans('textos.horarios_personalizados')) !!}" data-toggle="modal" data-target="#modal-custom-timetables" data-id="{{ $row->id }}" data-companyid="{{ $row->companyId }}">
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
@include('admin.employees.partials.modal-cancel-employee')

{{-- Custom Timetable: --}}
@include('admin.employees.partials.modal-custom-timetables')

@push('scripts')
<script>
$(function(){
	// Abreviaturas días de la semana:
	function weekDays_(day = false){
	    var days = {1: _lunes, 2: _martes, 3: _miercoles, 4: _jueves, 5: _viernes, 6: _sabado, 7: _domingo};

	    if(day){
	        return days[day];
	    }else{
	        return days;
	    }
	}

	// Bucle de opciones para tipo de jornada:
	function dayTypesOptions_(data, num, elem, text){
	    var html = '<div class="day-types-options">';
	        html += '<select class="form-control select2" name="" id="comboDayTypes" data-num="'+num+'">';
	            html += '<option value="">'+text+'</option>';
	            for(i=0; i<data.length; i++){
	                html += '<option value="'+data[i].id+'">'+data[i].name+'</option>';    
	            }
	        html += '</select>';

	        html += '<a href="#" data-num="'+num+'">'
	            html += '<i class="fa fa-times"></i>';
	        html += '</a>';
	    html += '</div>';

	    elem.append(html);    
	}

	// Modal customización horarios empleado.
    $(document).on('click', '.btnCustomTimetables', function(){
        var user_id = $('#userId').val();
        var id = $(this).data('id');
        var company_id = $(this).data('companyid');
        $('#employeeId').val(id); 
        $('#companyId').val(company_id);   
        $('#tblCustomTimetables').hide();
        options_data = false;

        $.get("/admin/user-timetables/"+user_id+"/show/"+id, function(response, state){ 
            //Info de user_timetables:
            if(response[1].length == 0){
                var from = response[0].from;
                var to = response[0].to? response[0].to:'';

            }else{
                $('#tblCustomTimetables').fadeIn();
                $('#tblCustomTimetables tbody').html('<tr><td colspan="4" class="text-center pb-3 pt-3"><i class="fas fa-spinner fa-spin"></i></td></tr>');

                var html = '';
                for(i=0; i<response[1].length; i++){
                    html += '<tr>';
                        html += '<td class="text-right">'+response[1][i].desde+'</td>';
                        html += '<td class="text-right">'+response[1][i].hasta+'</td>';
                        html += '<td>';
                            var hours = '';
                            var str_hours = '';
                            var k = 1;
                            for(const dh in response[1][i].daily_hours){
                                if(response[1][i].daily_hours[dh]){
                                    if(k > 1){
                                        hours += ', ';
                                    }

                                    var day = weekDays_(dh);
                                    var day_hours = response[1][i].daily_hours[dh].startsWith('_')? txtLaborableNo:response[1][i].daily_hours[dh];
                                    hours += day+': '+day_hours;
                                    k++;
                                }

                                if(dh > 1){
                                    str_hours += '|';
                                }
                                str_hours += response[1][i].daily_hours[dh]? dh+'-'+response[1][i].daily_hours[dh]:dh+'-0';
                            }
                            html += hours;
                        html += '</td>';
                        html += '<td class="text-right">';
                            //Editar: 
                            html += '<a href="#" class="btn btn-sm btn-info btnEditCustomTimetable" rel="tooltip" title="'+txtEditar+'" data-utid="'+response[1][i].id+'" data-from="'+response[1][i].desde+'" data-to="'+response[1][i].hasta+'" data-hours="'+str_hours+'"><i class="fa fa-edit"></i></a> ';

                            //Eliminar:
                            html += '<a href="/admin/user-timetables/'+response[1][i].id+'/delete" class="btn btn-sm btn-danger btn-confirm" rel="tooltip" title="'+txtEliminar+'" data-msg="'+txtHorariosPersonalesConfirmEliminar+'"><i class="fa fa-trash"></i></a>';
                        html += '</td>';
                    html += '</tr>';
                }

                $('#tblCustomTimetables tbody').html(html);
            }

            $('#from').val(from);
            $('#to').val(to);
        });
    });

    // Editar horario customizado de empleado desde el modal.
    $(document).on('click', '.btnEditCustomTimetable', function(e){
        e.preventDefault();
        var ut_id = $(this).data('utid');
        var from = $(this).data('from');
        var to = $(this).data('to');
        var str_hours = $(this).data('hours');

        $('#utId').val(ut_id);
        $('#from').val(from);
        $('#to').val(to);

        var hours = str_hours.split('|');
        for(const h in hours){
            var p = hours[h].split('-');
            if(p[1].startsWith('_')){
                $('#jornada_'+p[0]).prop('readonly', true);
                var day_type_id = p[1].substr(1);
                $('#dayType_'+p[0]).val(day_type_id);
            }else{
                $('#jornada_'+p[0]).val(p[1]);
            }
        }
    });

	// Determinar tipo de jornada para días de calendario personalizado.
	var options_data = false;
    $('.btnDayType').on('click', function(e){
        e.preventDefault();
        $('.day-types-options').fadeOut().remove();
        $('.jornada').prop('readonly', false);
        var elem = $(this);
        var num = $(this).data('num');
        var company_id = $('#companyId').val();
        $('#jornada_'+num).prop('readonly', true).val('');

        if(options_data){
            dayTypesOptions_(options_data, num, elem.parent(), txtOpcionSelect);

        }else{
            $.get("/admin/day-types/"+company_id+"/get", function(response, state){
                if(response){
                    options_data = response;
                    dayTypesOptions_(response, num, elem.parent(), txtOpcionSelect);
                }
            });
        }
    });
    // Cerrar selector de tipos de jornada.
    $(document).on('click', '.day-types-options a', function(e){
        e.preventDefault();   
        var num = $(this).data('num'); 
        $('.day-types-options').fadeOut().remove();
        $('#jornada_'+num).prop('readonly', false);
    });
    // Pasar valor del tipo de jornada al formulario.
    $(document).on('change', '#comboDayTypes', function(event){
        var id = event.target.value;
        var option = $("#comboDayTypes option:selected").text();
        var num = $(this).data('num');
        
        var html = '<div class="day-type-text">'+option+' <i class="fa fa-times"></i></div>';

        $('#jornada_'+num).prop('readonly', true).val('');
        $('#jornada_'+num).parent().find('.day-type-text').remove();
        $('#jornada_'+num).parent().append(html);
        $('#dayType_'+num).val(id);
    });
    $(document).on('click', '.day-type-text .fa', function(){
        $(this).parent().remove();
    });
});
</script>
@endpush

@endsection
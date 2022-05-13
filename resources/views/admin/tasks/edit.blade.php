@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">	
		@can('projects.edit')
			<a href="{{ route('projects.edit', [$task->project_id, 'tasks']) }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_proyecto') !!}
			</a>

			{{-- Tiempo trabajado --}}
			<a href="" class="btn btn-info btnWorkingTime" data-task="{{ $task->id }}" data-toggle="modal" data-target="#modal-add-working-time" rel="tooltip" title="{{ trans('textos.tiempo_trabajo') }}">
				<i class="fa fa-clock"></i>
			</a>

			{{-- Nuevo mensaje --}}
			<a href="#" class="btn btn-info btnResetReceivers" rel="tooltip" title="{!! trans('textos.mensaje_nuevo') !!}" data-toggle="modal" data-target="#modal-new-message">
				<i class="fa fa-envelope"></i>
			</a>

			{{-- Eliminar tarea --}}
			<a href="{{ route('tasks.destroy', $task) }}" class="btn btn-danger btn-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.tarea_eliminar')) }}" data-msg="{{ trans('textos.tarea_confirm_eliminar') }}">
				<i class="fa fa-trash"></i>
			</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-xl-8">
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-3">
					{!! ucfirst(trans('textos.tarea')) !!} 
					<u>{{ $task->name }}</u>
					({!! ucfirst(trans('textos.proyecto')) !!}: {{ $project->name }})
				</h3>

				{{-- Info --}}
				<div class="mb-3">
					{{-- Fecha alta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.creado')) !!}: 
						<b>
							{{ $task->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Última actualización --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.actualizado')) !!}: 
						<b>
							{{ $task->updated_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>
				</div>

				<form method="post" autocomplete="off" action="{{ route('tasks.update', $task) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Título tarea --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.tarea')) !!}*</label>
						<div class="col-lg-10">
							<x-input type="text" name="name" value="{!! $task->name !!}" autocomplete="off" maxlength="255" required/>
						</div>
					</div>

					<div class="form-group row">
                        {{-- Asignar a --}}
                        <label for="name" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.asignar_a')) !!}</label>
						<div class="col-md-4">
							@php
                            $members_ = [];

                            foreach($team_members as $tm){
                                $members_[$tm->id] = $tm->name.' '.$tm->surname;
                            }
                            @endphp

                            <x-select name="assigned_to" class="select2" label="usuario_selec" :options="$members_" value="{{ $task->assigned_to }}"/>
						</div>

                        {{-- Estado --}}
                    	<label for="status" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.estado')) !!}</label>
						<div class="col-md-4">
							@php
                            $task_status_ = [];

                            if($task_status->count()){
                                foreach($task_status as $ps){
                                    $task_status_[$ps->id] = $ps->name;
                                }
                                $label = 'inicio';
                            }else{
                                $task_status_['*'] = ucfirst(trans('textos.inicio'));    
                                $label = '';
                            }
                            @endphp

                            <x-select name="status" id="selectTaskStatus" class="select2" label="{{ $label }}" :options="$task_status_" value="{{ $task->status }}"/>
						</div>
                    </div>

                    <div class="form-group row">
                        {{-- Fecha entrega --}}
                        <label for="delivery_date" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.fecha_entrega')) !!}</label>
                        <div class="col-md-4 col-lg-2">
                        	@php
							$deliveryDate = $task->delivery_date != ''? \Carbon\Carbon::parse($task->delivery_date)->format($languages[session()->get('locale')][4]):'';
							@endphp

                            <x-date-picker name="delivery_date" id="delivery_date" :format="$languages[config('app.locale')][4]" value="{{ $deliveryDate }}"/>
                        </div>

                        {{-- Horas previstas --}}
                        <label for="estimated_hours" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.horas_previstas')) !!}</label>
                        <div class="col-md-4 col-lg-2">
                        	{{-- Convertimos los minutos a horas --}}
                        	@php
                        	$estimated_hours = $task->estimated_hours && $task->estimated_hours > 0? number_format($task->estimated_hours/60, 2):'';
                        	@endphp

                            <x-input type="text" name="estimated_hours" class="text-right set-decimals" autocomplete="off" maxlength="7" value="{{ $estimated_hours }}" info="task-estimated-hours"/>
                        </div>

                        {{-- Horas acumuladas --}}
                        <label for="hours_" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.horas_acumuladas')) !!}</label>
                        <div class="col-md-4 col-lg-2">
                        	@php
                        	$hours = $task->hours? number_format($task->hours/60, 2):'';
                        	@endphp

                        	<x-input type="text" name="hours_" class="text-right set-decimals" autocomplete="off" maxlength="7" value="{{ $hours }}" readonly/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Descripción --}}
                        <label for="description" class="col-lg-12">{!! ucfirst(trans('textos.descripcion')) !!}</label>
                        <div class="col-lg-12">
                            <x-input-textarea name="description" class="editor" value="{{ $task->description }}"/>
                        </div>
                    </div>

					<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
	
	<div class="col-12 col-md-6 col-xl-4">
		{{-- Charts --}}
		<div class="card mb-4">
			<div class="card-body">
				<div class="chart">
					<p><b>{{ ucfirst(trans('textos.tarea')) }}</b></p>
                	<canvas id="chartTaskDevelopment"></canvas>
                </div>

                <div class="chart mt-4">
					<p><b>{{ ucfirst(trans('textos.proyecto')) }}</b></p>
					<canvas id="chartProjectPercent"></canvas>
				</div>
            </div>
		</div>

		{{-- Mensajes --}}
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-3">
					{!! ucfirst(trans('textos.mensajes')) !!}
				</h3>

				@if($messages->count())
					@foreach($messages as $msg)
						@php
						$destinatarios = false;
						@endphp

						<div class="message">
							<label class="mr-4">
								<i class="fa fa-user"></i> {{ $msg->name }} {{ $msg->surname }}
							</label>

							<label>
								<i class="fa fa-calendar"></i> 
									{{ $msg->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}
							</label>

							<div class="message-content">
								@if($msg->to)
									@php
									$destinatarios = unserialize($msg->to);
									@endphp

									@foreach($team_members as $tm)
										@if(in_array($tm->id, $destinatarios))
											<span class="text-warning">&#64;{{ $tm->name }}</span>
										@endif
									@endforeach
								@endif

								{!! $msg->message !!}
								<div class="clearfix"></div>
							</div>
							<hr>

							<p class="text-right">
								{{-- Responder --}}
								<a href="" class="btnAddReceiver" data-toggle="modal" data-receivers="{{ $msg->from }}" data-target="#modal-new-message" rel="tooltip" title="{{ trans('textos.responder') }}"><i class="fa fa-reply"></i></a>

								@php
								$receivers = $destinatarios? implode(',', $destinatarios):'';
								$receivers .= ','.$msg->from;
								@endphp

								{{-- Responder a todos --}}
								<a href="" class="btnAddReceiver ml-2" data-receivers="{{ $receivers }}" data-toggle="modal" data-target="#modal-new-message" rel="tooltip" title="{{ trans('textos.responder_todos') }}"><i class="fa fa-reply-all"></i></a>

								{{-- Eliminar mensaje --}}
								@if(Auth::user()->id == $msg->from)
									<a href="{{ route('task-messages.destroy', $msg->id) }}" class="btn-confirm ml-2" data-msg="{{ ucfirst(trans('textos.mensaje_confirm_eliminar')) }}" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}"><i class="fa fa-trash"></i></a>	
								@endif
							</p>
						</div>
					@endforeach
				@else
					<label class="text-warning mt-3" style="font-size: 16px;"><b>{!! trans('textos.mensajes_no_hay') !!}</b></label>	
				@endif
			</div>
		</div>
	</div>
</div>

{{-- Info para charts --}}
@php
$completed_task = $task->estimated_hours && $task->estimated_hours > 0? ($task->hours * 100) / $task->estimated_hours:'0';
$pending_task = $completed_task != '' && $completed_task >= 100? 0:100 - $completed_task;

//Horas previstas del proyecto: $project->hours_forecast
//Porcentaje que representa la tarea sobre el total de horas ya empleadas. $task->hours se recibe en minutos, de ahí que se convierta a horas.
$task_over_project = $project->hours_forecast > 0? ( ($task->hours / 60) * 100) / $project->hours_forecast:'0';	
//Porcentaje de proyecto completado:
$completed_project = $project->hours_forecast > 0? (($project->hours_worked * 100) / $project->hours_forecast) - $task_over_project:'0';
@endphp

{{-- Modals --}}
{{-- Add working time: --}}
@include('admin.tasks.partials.modal-add-working-time', ['return' => 'task'])

{{-- New message: --}}
@include('admin.tasks.partials.modal-add-message')

@push('scripts')
<script src="{{ asset('js/plugins/pace/pace.min.js') }}"></script>
<script src="{{ asset('js/plugins/chartJs/Chart.min.js') }}"></script>

<script>
$(function(){
	//Porcentaje completado de la tarea:
	var chartTaskDevelopment = {
        labels: [txtCompletado+' {{ number_format($completed_task,2) }}%', txtPendiente+' {{ number_format($pending_task, 2) }}%'],
        datasets: [{
            data: [{{ number_format($completed_task, 2) }}, {{ number_format($pending_task, 2) }}],
            backgroundColor: [color_chart_success,color_chart_danger]
        }]
    };

    var chartTaskDevelopmentOptions = {
        responsive: true,
        //animateRotate: true
    };

    var taskDevelopment = document.getElementById("chartTaskDevelopment").getContext("2d");
    new Chart(taskDevelopment, {type: 'doughnut', data: chartTaskDevelopment, options: chartTaskDevelopmentOptions});

    //Porcentaje sobre el proyecto:
    var chartProjectPercent = {
    	labels: [txtHorasPrevistas+' {{ $project->hours_forecast }} ', txtCompletado+' {{ number_format($completed_project,2) }}%', txtTarea+' {{ number_format($task_over_project, 2) }}%'],
    	datasets: [{
            data: [{{ $project->hours_forecast }}, {{ number_format($completed_project, 2) }}, {{ number_format($task_over_project, 2) }}],
            backgroundColor: [color_chart_danger, color_chart_success, color_chart_grey]
        }]
    }

    var chartProjectPercentOptions = {
        responsive: true,
        //animateRotate: true
    };

    var ProjectPercent = document.getElementById("chartProjectPercent").getContext("2d");
    new Chart(ProjectPercent, {
    	type: 'pie', 
    	data: chartProjectPercent, 
    	options: chartProjectPercentOptions
    });

    // Seleccionar destinatarios en mensaje de tarea.
    $(document).on('click', '.btnAddReceiver', function(){
        $('#selectReceivers').val(null).trigger('change');
        var receivers = $(this).data('receivers');
        receivers = String(receivers);
        var arr = receivers.split(",");

        //Atención al modo de marcar como selected las opciones en un Select2:
        $('#selectReceivers').val(arr);
        $('#selectReceivers').trigger('change');
    });
    $(document).on('click', '.btnResetReceivers', function(){
        $('#selectReceivers').val(null).trigger('change');
    });
});
</script>
@endpush

@endsection
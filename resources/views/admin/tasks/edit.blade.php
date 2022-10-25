@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">	
		@can('projects.edit')
			{{-- Volver a proyecto --}}
			<a href="{{ route('projects.edit', [$task->project_id, 'tasks']) }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_proyecto') !!}
			</a>

			{{-- Nueva subtarea --}}
			<a href="#" class="btn btn-info" rel="tooltip" data-toggle="modal" data-target="#modal-add-subtask">
				<i class="fa fa-plus"></i>
					{!! ucfirst(trans('textos.subtarea_nueva')) !!}
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

				{{-- Tabs: --}}
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item @if(!$tab) active @endif">
			    		<a class="nav-link @if(!$tab) active @endif" href="#tab-info" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.tarea_info')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'subtasks') active @endif">
			  			<a class="nav-link @if($tab == 'subtasks') active @endif" href="#tab-subtasks" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.subtareas')) !!}</a>	
			  		</li>
				</ul>

				<div class="tab-content pt-4">
					{{-- Info tarea --}}
					<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-info">
						@include('admin.tasks.partials.task-info')	
					</div>

					{{-- Subtareas --}}
					<div class="tab-pane fade @if($tab == 'subtasks') show active @endif" role="tabpanel" id="tab-subtasks">
						@include('admin.tasks.partials.task-subtasks')	
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="col-12 col-md-6 col-xl-4">
		{{-- Charts --}}
		<div class="card mb-4">
			<div class="card-body">
				<div class="chart chart-mini" style="position: relative;">
					<p><b>{{ ucfirst(trans('textos.tarea')) }}</b></p>
                	<canvas id="chartTaskDevelopment"></canvas>
                </div>

                <div class="chart chart-mini mt-4" style="position: relative;">
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

{{-- New subtask: --}}
@include('admin.tasks.partials.modal-add-subtask')

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
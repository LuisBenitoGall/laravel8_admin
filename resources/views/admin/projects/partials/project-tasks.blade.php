@php
$columns_index = [];
//dd($task_status);
@endphp

@foreach($task_status as $ps)
	@php
	$columns_index[$ps->id] = '';
	@endphp
@endforeach

@if($tasks->count())
	@foreach($tasks as $task)
		@if($task->status)
			@php
			$html = '<li class="task" id="'.$task->id.'">';
				$html .= '<a href="'.route('tasks.edit', $task->id).'"><h5>'.ucfirst($task->name).'</h5></a>';
				
				if($task->assigned_to){
					$html .= '<p><i class="fa fa-user mr-1"></i> '.$task->Nombre.' '.$task->surname.'</p>';
				}

				if($task->delivery_date > 0){
					$delivery_date = $task->delivery_date != ''? \Carbon\Carbon::parse($task->delivery_date)->format($languages[session()->get('locale')][4]):'';

					//Fecha vencida:
					$expired = $task->delivery_date < date('Y-m-d')? 'text-danger':false;
					$title = $expired? trans('textos.fecha_vencida'):false;

					$html .= '<p class="'.$expired.'" rel="tooltip" title="'.$title.'"><i class="fa fa-calendar mr-1"></i> '.$delivery_date.'</p>';
				}

				// Horas estimadas / horas trabajadas:
				if($task->estimated_hours && $task->estimated_hours > 0){
					$estimated_hours = $task->estimated_hours? App\Helpers\helpers::getTimeFromMinutes($task->estimated_hours):false;
					$worked_hours = $task->hours? App\Helpers\helpers::getTimeFromMinutes($task->hours):false; 

					//Horas consumidas:
					$expended = $worked_hours > 0 && $estimated_hours > 0? ((int)$worked_hours * 100) / (int)$estimated_hours:'0';
					$expended_class = '';
					if($expended >= 80 && $expended < 100){
						$expended_class = 'text-warning';	
					}elseif($expended >= 100){
						$expended_class = 'text-danger';
					}

					$html .= '<p><i class="fa fa-clock mr-1"></i> <span>'.$estimated_hours.'h.';
					if($worked_hours){
						$html .= ' / <span class="'.$expended_class.'">'.$worked_hours.'h.</span>';
					}

					//Añadir horas de trabajo.
					$html .= '<a href="" class="float-right text-info btnWorkingTime" data-task="'.$task->id.'" data-toggle="modal" data-target="#modal-add-working-time" rel="tooltip" title="'.trans('textos.tiempo_trabajo').'"><i class="fa fa-plus-circle"></i></a>';
					$html .= '</p>';
				}

				$image = $task->lastImage();
				if($image){
					$html .= '<div class="mt-1"><img src="'.asset('img/tasks/'.$image->doc).'" class="img-fluid"></div>';
				}

				// Descripción:
				if($task->description){
					$text = App\Helpers\Helpers::truncate($task->description, 200);
					$html .= '<hr><div>'.$text.'</div>';
				}

				// Eliminar tarea:
				$html .= '<div class="mt-2 mb-3"><a href="'.route('tasks.destroy', $task->id).'" class="btn-confirm float-right text-danger" rel="tooltip" title="'.ucfirst(trans('textos.tarea_eliminar')).'" data-msg="'.trans('textos.tarea_confirm_eliminar').'"><i class="fa fa-trash"></i></a></div>';
			$html .= '</li>'; 

			if(!$task->status){
				$columns_index[0] .= $html;
			}else{
				$columns_index[$task->status] .= $html;
			}
			@endphp
		@endif
	@endforeach

	{{-- Creamos las columnas html a partir del número del número de columnas-estado que tenga el proyecto  --}}
	<div class="scroll-top">
	    <div class="scroll-top-row">
	    </div>
	</div>

	<div id="tasks" class="scroll-bottom">
		<div class="tasks-wrapper">
			@foreach($task_status as $col)
				<div class="task-column mt-3 mb-2">
					<h4>{{ ucfirst($col->name) }}</h4>	
				</div>
			@endforeach

			@foreach($task_status as $col)
				<ul class="task-column droptrue" id="{{ $col->id }}">
					{{-- Añadir tarea en la columna --}}
					<li class="add-task text-center">
						<a href="#" class="btnAddTask" data-toggle="modal" data-target="#modal-add-task" data-status="{{ $col->id }}">
							<i class="fa fa-plus"></i> {{ ucfirst(trans('textos.tarea_nueva')) }}
						</a>
					</li>

					{!! $columns_index[$col->id] !!}
				</ul>
			@endforeach

			<div class="clearfix"></div>
		</div>	
	</div>
@endif
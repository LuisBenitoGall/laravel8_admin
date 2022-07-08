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
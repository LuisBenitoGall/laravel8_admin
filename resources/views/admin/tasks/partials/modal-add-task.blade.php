<div class="modal fade modal-save-task" id="modal-add-task" tabindex="-1" role="dialog" aria-labelledby="addTask" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addTask">{!! ucfirst(trans('textos.tarea_nueva')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
				<form method="post" autocomplete="off" action="{{ route('tasks.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" value="{{ $project->id }}">

					<div class="form-group row">
						{{-- Título tarea --}}
						<label for="name" class="col-lg-12">{!! ucfirst(trans('textos.tarea')) !!}*</label>
						<div class="col-lg-12">
							<x-input type="text" name="name" autocomplete="off" maxlength="255" required/>
						</div>
					</div>

					<div class="form-group row">
                        {{-- Asignar a --}}
                        <div class="col-md-6">
                            <div class="row">
                                <label for="assigned_to" class="col-lg-12">{!! ucfirst(trans('textos.asignar_a')) !!}</label>
                                <div class="col-lg-12">
                                    @php
                                    $members_ = [];

                                    foreach($team_members as $tm){
                                        $members_[$tm->id] = $tm->name.' '.$tm->surname;
                                    }
                                    @endphp

                                    <x-select name="assigned_to" class="select2" label="usuario_selec" :options="$members_"/>
                                </div>
                            </div>
                        </div>

                        {{-- Estado --}}
                        <div class="col-md-6">
                            <div class="row">
                                <label for="status" class="col-lg-12">{!! ucfirst(trans('textos.estado')) !!}*</label>
                                <div class="col-lg-12">
                                    @php
                                    $task_status_ = [];

                                    if($task_status->count()){
                                        foreach($task_status as $ps){
                                            $task_status_[$ps->id] = $ps->name;
                                        }
                                    }else{
                                        $task_status_['*'] = ucfirst(trans('textos.inicial'));    
                                    }
                                    @endphp

                                    <x-select name="status" id="selectTaskStatus" class="select2" label="" :options="$task_status_" required/>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Fecha entrega --}}
                        <div class="col-md-6">
                            <div class="row">
                                <label for="delivery_date" class="col-lg-12">{!! ucfirst(trans('textos.fecha_entrega')) !!}</label>
                                <div class="col-lg-12">
                                    <x-date-picker name="delivery_date" id="delivery_date" :format="$languages[config('app.locale')][4]"/>
                                </div>
                            </div>
                        </div>

                        {{-- Horas previstas --}}
                        <div class="col-md-6">
                            <div class="row">
                                <label for="estimated_hours" class="col-lg-12">{!! ucfirst(trans('textos.horas_previstas')) !!}</label>
                                <div class="col-lg-12">
                                    <x-input type="text" name="estimated_hours" class="text-right set-decimals" autocomplete="off" maxlength="7"/>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Descripción --}}
                        <label for="description" class="col-lg-12">{!! ucfirst(trans('textos.descripcion')) !!}</label>
                        <div class="col-lg-12">
                            <x-input-textarea name="description" class="editor"/>
                        </div>
                    </div>

					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                </form>
            </div>
        </div>
    </div>
</div>
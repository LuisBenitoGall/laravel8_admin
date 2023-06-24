<div class="modal fade modal-set-group" id="modal-set-group" tabindex="-1" role="dialog" aria-labelledby="setGroup" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="setGroup">{!! ucfirst(trans('textos.proyectos_vinculados')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('project-groups.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="project_id" value="{{ $project->id }}">

                    <p class="text-warning">{!! ucfirst(trans('textos.proyectos_vinculados_texto')) !!}</p>

                    <div class="form-group row">
                        {{-- Nuevo grupo --}}
                        <label for="title" class="col-12">{{ ucfirst(trans('textos.grupo_nuevo')) }}</label>
                        <div class="col-12">
                            <x-input type="text" name="title" placeholder="{!! ucfirst(trans('textos.grupo_nuevo')) !!}" autocomplete="off" maxlength="150" info=""/>      
                        </div>
                    </div>
                    <hr>

                    <div class="form-group row">
                        @if(count($project_groups))
                            <label for="group" class="col-12">{{ ucfirst(trans('textos.grupo_selecciona')) }}</label>
                            <div class="col-12">
                                <x-select name="group" class="select2" :options="$project_groups" label="grupo_selecciona"/>
                            </div>
                        @else
                            <div class="col-12 text-center py-4">
                                <h3 class="text-warning">No hay agrupaciones de proyectos activas en este momento.</h3>
                            </div>
                        @endif
                    </div>

                    <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                </form>   
            </div>
        </div>
    </div>
</div>
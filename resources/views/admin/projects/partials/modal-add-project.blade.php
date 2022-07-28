<div class="modal fade modal-add-project" id="modal-add-project" tabindex="-1" role="dialog" aria-labelledby="addProject" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addProject">{!! ucfirst(trans('textos.proyecto_vincular')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
            	{{-- Nuevo proyecto --}}
	            <span class="text-warning mr-5" style="line-height: 36px;">{{ trans('textos.proyecto_nuevo_crea') }}</span>

            	<a href="{{ route('projects.create-from-budget-order', $order) }}" class="btn btn-info pull-right">
	    			<i class="fa fa-plus"></i>
	    			{!! ucfirst(trans('textos.proyecto_nuevo')) !!}
	    		</a>
		    	<div class="clearfix"></div>
	    		<hr>

            	<form method="post" autocomplete="off" action="{{ route('projects.link-project') }}" id="frmSaveChangeRound" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="order_id" value="{{ $order->id }}">

                    <p class="text-warning">{{ trans('textos.proyecto_vincular_texto') }}</p>

                    <div class="form-group row">
                        {{-- Proyectos --}}
                    	<label for="project_id" class="col-12">{{ ucfirst(trans('textos.proyecto')) }}</label>
                    	<div class="col-12">
                            @php
                            $projects_ = [];

                            foreach($projects as $project){
                                $projects_[$project->id] = $project->code.' '.$project->name;
                            }
                            @endphp

                            <x-select name="project_id" class="select2" label="proyecto_selec" :options="$projects_"/>
                    	</div>
                    </div>

                    <x-btn-submit name="submit_add_project" label="{{ trans('textos.guardar') }}"/>
                </form>   
            </div>
        </div>
    </div>
</div>
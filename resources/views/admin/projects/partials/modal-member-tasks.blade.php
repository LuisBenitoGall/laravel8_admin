<div class="modal fade modal-member-tasks" id="modal-member-tasks" tabindex="-1" role="dialog" aria-labelledby="memberTasks" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="memberTasks">{!! ucfirst(trans('textos.tareas_de')) !!} <b></b></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
            	<table class="modal-table" id="tblMemberTasks">
            		<thead>
            			<tr>
            				<th>{{ ucfirst(trans('textos.tarea')) }}</th>
            				<th class="text-center">{{ trans('textos.fecha_entrega') }}</th>
            				<th class="text-center">{{ ucfirst(trans('textos.horas')) }}</th>
            				<th>{{ trans('textos.estado') }}</th>
            				<th>{{ ucfirst(trans('textos.acciones')) }}</th>
            			</tr>
            		</thead>

            		<tbody>
            				
            		</tbody>
            	</table>
            </div>
        </div>
    </div>
</div>
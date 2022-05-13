<div class="modal fade modal-task-status" id="modal-task-status" tabindex="-1" role="dialog" aria-labelledby="taskStatus_" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="taskStatus_">{!! ucfirst(trans('textos.tareas_estados')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
				<form method="post" autocomplete="off" id="frmTaskStatus" action="#" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="order" id="projectStatusOrder" value="{{ $task_status->count() }}">
                    <input type="hidden" id="projectId" value="{{ $project->id }}">

                    <div class="form-group row">
                    	<p class="text-warning col-12">
                    		{!! trans('textos.crea_ordena_columnas') !!}		
                    	</p>

                    	<div class="col-lg-9 msgTaskStatus">
                    		<div class="row">
								<label for="name" class="col-lg-12">{!! ucfirst(trans('textos.estado_nuevo')) !!}*</label>
								<div class="col-lg-12">
									{{-- Estado --}}
									<x-input type="text" name="name" autocomplete="off" maxlength="255" required/>
								</div>
							</div>
						</div>

						<div class="col-lg-3" style="padding-top: 5px;">
							<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                    	</div>
					</div>
                </form>
                <hr>

                <ul id="taskStatus">
                	@foreach($task_status as $ts)
						<li id="item-{{ $ts->id }}">
							{{ ucfirst($ts->name) }}

							@if($task_status->count() > 1)
								<a href="{{ route('task-status.delete', [$project->id, $ts->id]) }}" class="btn-confirm" rel="tooltip" title="{{ trans('textos.columna_quitar') }}" data-msg="{{ trans('textos.columna_quitar_confirm') }}"><i class="fa fa-times"></i></a>
							@endif
						</li>
                	@endforeach
                </ul>
            </div>
        </div>
    </div>
</div>	

@push('scripts')
<script>
$(function(){
	// Guardar nuevo estado.
	$('#frmTaskStatus').on('submit', function(e){
        e.preventDefault();
        var project_id = $('#projectId').val();
        var data = $(this).serialize();

        $.ajax({
            url: "/admin/task-status/store",
            type: "POST",
            data: data, 
            cache: false,
            datatype: "JSON",
            success: function(response){
                if(response == 'duplicado'){
                    $('.msgTaskStatus').append('<small class="text-danger">No puede repetirse el nombre de las columnas</small>');    
                }else{
                    $('#frmTaskStatus [name=name]').val('');
                    html = '<li>'+response.name+' <a href="/admin/task-status/'+project_id+'/delete/'+response.id+'" class="btn-confirm" rel="tooltip" title="'+txtColumnaQuitar+'" data-msg="'+txtColumnaQuitarConfirm+'"><i class="fa fa-times"></i></a></li>';        

                    $('#taskStatus').append(html);
                }
            }
        });    
    });

    // Recargar edición de proyecto tras editar estados de las tareas. Detectamos el momento de cerrar el modal:
    $('#modal-task-status').on('hidden.bs.modal', function(){
        location.reload();     
    });

    // Ordenar columnas de estado de tareas:
    if($("#taskStatus").length > 0){
        $("#taskStatus").sortable({
            axis: 'y',
            update: function(event, ui){
                var items = $(this).sortable('serialize');
                $.ajax({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        _token:$('meta[name="csrf-token"]').attr('content'),
                        items:items,
                    },
                    type: 'POST',
                    url: '/admin/task-status/order-columns'
                });
            }    
        });
        $("#taskStatus").disableSelection();
    }
});
</script>

@endpush
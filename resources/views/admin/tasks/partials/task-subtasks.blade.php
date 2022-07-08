<div class="row">
	@if($subtasks->count())
		<div class="table-responsive">
			<table class="table table-hover dTable" id="tblSubtasks">
				<thead>
					<tr>
						<th class="text-center">{{ ucfirst(trans('textos.fecha')) }}</th>
						<th>{{ ucfirst(trans('textos.descripcion')) }}</th>
						<th class="text-center">{{ ucfirst(trans('textos.acciones')) }}</th>
					</tr>
				</thead>

				<tbody>
					@foreach($subtasks as $stk)
						<tr>
							{{-- Fecha --}}
							<td class="text-right wdt100">{{ $stk->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}</td>

							{{-- Descripción --}}
							<td class="wdt300">{{ $stk->description }}</td>

							{{-- Acciones --}}
							<td class="text-right wdt100">
								{{-- Estado --}}
								<label class="custom-chk-rdb" rel="tooltip" title="">
		                            <input type="checkbox" name="status" @if($stk->status) checked @endif><span class="chk chk-subtask" data-id="{{ $stk->id }}" data-ctrl="admin/subtasks/status"></span>
		                        </label>

		                        {{-- Eliminar --}}
		                        <form method="POST" action="{{ route('subtasks.destroy', $stk->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
		                            <input name="_method" type="hidden" value="DELETE">
		                            {{ csrf_field() }}
		                            <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ trans('textos.subtarea_eliminar') }}" data-msg="{{ trans('textos.subtarea_eliminar_confirm') }}"><i class="fa fa-trash"></i></button>
		                        </form>
							</td>
						</tr>
					@endforeach	
				</tbody>
			</table>
		</div>		

	@else
		<div class="col-12">
	        <h4 class="mb-5 mt-5 text-center text-warning">
	            {!! trans('textos.subtareas_no_definidas') !!}
	        </h4>
	    </div>
	@endif
</div>

@push('scripts')
<script>
$(function(){
	//Marcar / desmarcar tarea como realizada:
	$(document).on('click', '.chk-subtask', function(){
        var status = '';
        var elem = $(this);
        //Atención!!: detecta el estado previo a la acción, de modo que :checked es si queda deseleccionado:
        if(elem.siblings('input').is(':checked')){
            status = 0;
        }else{
            status = 1;
        }  
        var id = $(this).data('id');
        $.get("/admin/subtasks/status/"+id+"/"+status+"", function(response, state){
            if(response){
                $('.check-ko').remove();
                elem.parent('label').before('<i class="fa fa-check check-ok"></i>');
                $('.check-ok').delay(1000).fadeOut();
            }else{
                elem.parent('label').before('<i class="fa fa-exclamation-triangle check-ko"></i>');
            }
        }); 
    });
});
</script>
@endpush
@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="myFrom" value="{{ $from }}">
<input type="hidden" id="myTo" value="{{ $to }}">
<input type="hidden" id="myUser" value="{{ $user->id }}">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Edición usuario --}}
		<a href="{{ route('users.edit', $user->id) }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_editar_usuario') !!}
		</a>

	</div>
</div>

{{-- Filtro formulario: --}}
<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('users.filter-timetables') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}
					<input type="hidden" name="user_id" value="{{ $user->id }}">

					<div class="form-group row">
						{{-- Desde: --}}
						<label for="from" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.desde')) !!}</label>
						<div class="col-md-2 col-lg-2">
							@php
							$desde = $from? \Carbon\Carbon::parse($from)->format($languages[session()->get('locale')][4]):'';
							@endphp

							<x-date-picker name="from" class="text-right" :format="$languages[config('app.locale')][4]" required value="{{ $desde }}"/>
						</div>

						{{-- Hasta: --}}
						<label for="to" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.hasta')) !!}</label>
						<div class="col-md-2 col-lg-2">
							@php
							$hasta = $from? \Carbon\Carbon::parse($to)->format($languages[session()->get('locale')][4]):'';
							@endphp

							<x-date-picker name="to" class="text-right" :format="$languages[config('app.locale')][4]" required value="{{ $hasta }}"/>
						</div>

						<x-btn-submit name="submit_save" label="{{ trans('textos.buscar') }}" inline="true"/>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

{{-- Horarios --}}
<div class="row">
	<div class="col-12 mb-3">						
		<h3>{{ ucfirst(trans('textos.horarios')) }} <u>{{ $user->name }} {{ $user->surname }}</u></h3>	
	</div>

	@if($contract)
		<div class="table-responsive">
			<table class="table table-hover dtabla" id="tblUserTimetables">
				<thead>
					<tr>
						<th class="text-center">{!! ucfirst(trans('textos.fecha')) !!}</th>
						<th class="text-center">{!! ucfirst(trans('textos.hora')) !!}</th>
						<th>{!! ucfirst(trans('textos.concepto')) !!}</th>
						<th class="text-center">{!! ucfirst(trans('textos.tiempo_acumulado')) !!}</th>
						<th>{!! ucfirst(trans('textos.observaciones')) !!}</th>
						<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
					</tr>
				</thead>

				<tfoot>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td class="text-right" id="totals-footer">
							
						</td>
						<td></td>
						<td></td>
					</tr>	
				</tfoot>
			</table>
		</div>
	@else
		@if(!$contract)
			<h5 class="text-warning mt-5">
				{!! trans('textos.usuario_sin_contrato_texto') !!}
				@can('employees.create')
					<a href="{{ route('employees.create', $user) }}" class="btn btn-info ml-3">
						<i class="fa fa-plus"></i>
						{{ ucfirst(trans('textos.contrato_nuevo')) }}
					</a>
				@endcan
			</h5>
		@endif

		@if(!$workplace)
			<h5 class="text-warning mt-5">
				{!! trans('textos.usuario_sin_centro_trabajo') !!}
				@can('employees.create')
					<a href="{{ route('users.edit', [$user->id, 'company']) }}" class="btn btn-info ml-3">
						<i class="fa fa-user"></i>
						{{ ucfirst(trans('textos.usuario_editar')) }}
					</a>
				@endcan
			</h5>
		@endif
	@endif
</div>

{{-- Modals --}}
{{-- Editar registro horario: --}}
@include('admin.users.partials.modal-edit-user-timetable')

@push('scripts')
<script>
$(function(){
	// Editar registro horario.
    $(document).on('click', '.btnRegistroHorario', function(e){
        e.preventDefault();
        var id = $(this).data('id');
        var concepto = $(this).data('concepto');
        concepto = concepto.charAt(0).toUpperCase()+concepto.slice(1);
        var icono = $(this).data('icono');
        var flag = $(this).data('flag');
        $('#timetableItems').empty();

        $.get("/admin/timetable/"+id+"/edit-hour", function(response, state){
            if(response){ 
                if(flag){
                    $.each(timetableItems, function(i, item){
                        if(item.type == flag){
                            var option = '<option value="'+item.id+'">'+item.name+'</option>';
                            $('#timetableItems').append(option);
                        }
                    });
                }

                //Atención al modo de marcar como selected las opciones en un Select2:
                $('#timetableItems').val(response.item_id);
                $('#timetableItems').trigger('change');

                var registro = '<i class="fa fa-'+icono+'"></i> '+concepto;
                $('#tmConcepto').html(registro);

                $('#tmId').val(response.id);
                $('#tmComments').val(response.comments);

                var h = response.hour.split(':');
                $('#tmHora').val(h[0]);
                $('#tmMinutos').val(h[1]);
                $('#tmSegundos').val(h[2]);
            }
        });
    });

});
</script>
@endpush

@endsection
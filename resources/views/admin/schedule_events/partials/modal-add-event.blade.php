<div class="modal fade modal-add-event" id="modal-add-event" tabindex="-1" role="dialog" aria-labelledby="addEvent" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addEvent">
                	<i class="fa fa-calendar-check"></i>
                	{!! ucfirst(trans('textos.evento_add')) !!} 
                	<u class="text-warning" id="when"></u>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div wire:ignore.self class="modal-body">
                <form wire:submit.prevent="store" method="post" autocomplete="off" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}

                    {{-- Agenda --}}
                    @if($schedules->count())
                    	<div class="form-group row">
	                        <label for="schedule_id" class="col-lg-12">{!! ucfirst(trans('textos.agenda')) !!}*</label>
	                        <div class="col-lg-12">
	                        	<select name="schedule_id" class="form-control select2_scheduleId" required wire:model.defer="schedule_id">
		                            <option value="">{{ trans('textos.agenda_selecciona') }}</option>  
		                            @foreach($schedules as $sd)
		                                <option value="{{ $sd->id }}">{{ $sd->name }}</option>    
		                            @endforeach
		                        </select>
	                        </div>
	                    </div>
                    @endif

                    {{-- Tipo de eventos --}}
                    @if(count($event_types))
	                    <div class="form-group row">
	                        <label for="event_type_id" class="col-lg-12">{!! ucfirst(trans('textos.evento_tipo')) !!}</label>
	                        <div class="col-lg-12">
	                        	<x-select name="event_type_id" class="select2_eventType" :options="$event_types" label="evento_tipo_selec" wire="true"/>
	                        </div>
	                    </div>
	                @endif

	                {{-- Título de evento --}}
                    <div class="form-group row">
                        <label for="title" class="col-lg-12">{!! ucfirst(trans('textos.titulo')) !!}*</label>
                        <div class="col-lg-12">
                        	<x-input type="text" name="title" maxlength="255" autocomplete="off" required wire="true"/>
                        </div>
                    </div>

                    {{-- Descripción --}}
                    <div class="form-group row">
                        <label for="description" class="col-lg-12">{!! ucfirst(trans('textos.descripcion')) !!}</label>
                        <div class="col-lg-12">
                        	<x-input-textarea name="description"/>
                        </div>
                    </div>

                    <div class="form-group row">
                    	{{-- Inicio --}}
                    	<div class="col-md-6">
                    		<div class="row">
		                        <label for="start" class="col-lg-12">{!! ucfirst(trans('textos.inicio')) !!}</label>
		                        <div class="col-lg-12">
		                        	<x-input type="text" name="start" class="text-right" id="startTime" maxlength="191" autocomplete="off" required/>
		                        </div>
		                    </div>
		                </div>

		                {{-- Fin --}}
						<div class="col-md-6">
		                    <div class="row">
		                        <label for="end" class="col-lg-12">{!! ucfirst(trans('textos.fin')) !!}</label>
		                        <div class="col-lg-12">
		                        	<x-input type="text" name="end" class="text-right" id="endTime" maxlength="191" autocomplete="off" required/>
		                        </div>
		                    </div>
	                    </div>
                    </div>

					@if($users->count())
						{{-- Responsables del evento: --}}
						<div class="form-group row">
	                        <label for="responsible" class="col-lg-12">{!! ucfirst(trans('textos.responsables')) !!}</label>
	                        <div class="col-lg-12">
	                        	@php
	                        	$users_ = [];
	                        	foreach($users as $u){
	                        		$users_[$u->id] = $u->name.' '.$u->surname;
	                        	}
	                        	@endphp

	                        	<x-select name="responsible[]" class="select2" :options="$users_" :values="[Auth()->user()->id]" multiple/>
	                        </div>
	                    </div>

						{{-- Invitados: --}}
	                    <div class="form-group row">
	                        <label for="guests" class="col-lg-12">{!! ucfirst(trans('textos.invitar_a')) !!}</label>
	                        <div class="col-lg-12">
	                        	<x-select name="guests[]" class="select2" :options="$users_" multiple/>
	                        </div>
	                    </div>
	                @endif

	                {{-- Productos: --}}
	                @if($products)
	                	<div class="form-group row">
	                        <label for="products" class="col-lg-12">{!! ucfirst(trans('textos.productos')) !!}</label>
	                        <div class="col-lg-12">
	                        	<x-select name="products[]" class="select2" :options="$products" multiple/>
	                        </div>
	                    </div>	
	                @endif

	                {{-- Lugar --}}
	                {{-- 24/08/2022: implementar buscador Google maps --}}
                    <div class="form-group row">
                        <label for="place" class="col-lg-12">{!! ucfirst(trans('textos.lugar')) !!}</label>
                        <div class="col-lg-12">
                        	<x-input type="text" name="place" maxlength="255"/>
                        </div>
                    </div>

                    <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                </form>    
            </div>
        </div>
    </div>
</div>
<div wire:ignore.self class="modal fade modal-add-event" id="modal-add-event" tabindex="-1" role="dialog" aria-labelledby="addEvent" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addEvent">
                	<i class="fa fa-calendar-check"></i>
                	{!! ucfirst(trans('textos.evento_add')) !!} 
                	<u class="text-warning" id="when">{{ $mywhen }}</u>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form @if($lw) wire:submit.prevent="store" @else action="{{ route('schedule-events.store') }}" @endif method="post" autocomplete="off" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}

                    {{-- Agenda --}}
                    @if($schedules->count())
                    	<div class="form-group row">
	                        <label for="schedule_id" class="col-lg-12">{!! ucfirst(trans('textos.agenda')) !!}*</label>
	                        <div class="col-lg-12" wire:ignore>
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
	                        <div class="col-lg-12" wire:ignore>
	                        	<select name="event_type_id" class="form-control select2_eventTypeId" wire:model.defer="event_type_id">
		                            <option value="">{{ trans('textos.evento_tipo_selec') }}</option>  
		                            @foreach($event_types as $i => $et)
		                                <option value="{{ $i }}">{{ $et }}</option>    
		                            @endforeach
		                        </select>
	                        </div>
	                    </div>
	                @endif

	                {{-- Título de evento --}}
                    <div class="form-group row">
                        <label for="title" class="col-lg-12">{!! ucfirst(trans('textos.titulo')) !!}*</label>
                        <div class="col-lg-12" wire:ignore>
                        	<x-input type="text" name="title" maxlength="255" autocomplete="off" required wire="true"/>
                        </div>
                    </div>

                    {{-- Descripción --}}
                    <div class="form-group row">
                        <label for="description" class="col-lg-12">{!! ucfirst(trans('textos.descripcion')) !!}</label>
                        <div class="col-lg-12" wire:ignore>
                        	<x-input-textarea name="description" wire="true"/>
                        </div>
                    </div>

                    <div class="form-group row">
                    	{{-- Inicio --}}
                    	<div class="col-md-6">
                    		<div class="row">
		                        <label for="start" class="col-lg-12">{!! ucfirst(trans('textos.inicio')) !!}</label>
		                        <div class="col-md-8" wire:ignore>
		                        	<x-input type="text" name="startDate" class="text-right" id="startDate" maxlength="25" autocomplete="off" required wire="true"/>
		                        </div>
		                        <div class="col-md-4" wire:ignore>
		                        	<x-input type="text" name="startTime" class="text-right" id="startTime" maxlength="9" autocomplete="off" required wire="true"/>
		                        </div>
		                    </div>
		                </div>

		                {{-- Fin --}}
						<div class="col-md-6">
		                    <div class="row">
		                        <label for="end" class="col-lg-12">{!! ucfirst(trans('textos.fin')) !!}</label>
		                        <div class="col-md-8" wire:ignore>
		                        	<x-input type="text" name="endDate" class="text-right" id="endDate" maxlength="25" autocomplete="off" required wire="true"/>
		                        </div>
		                        <div class="col-md-4" wire:ignore>
		                        	<x-input type="text" name="endTime" class="text-right" id="endTime" maxlength="9" autocomplete="off" required wire="true"/>
		                        </div>
		                    </div>
	                    </div>
                    </div>

					@if($users->count())
						{{-- Responsables del evento: --}}
						<div class="form-group row">
	                        <label for="responsible" class="col-lg-12">{!! ucfirst(trans('textos.responsables')) !!}</label>
	                        <div class="col-lg-12" wire:ignore>
	                        	<select name="responsible[]" class="form-control select2_responsible" wire:model.defer="responsible" multiple>
		                        	@foreach($users as $u)
		                        		<option value="{{ $u->id }}">{{ $u->name }} {{$u->surname }}</option>
		                        	@endforeach
		                        </select>
	                        </div>
	                    </div>

						{{-- Invitados: --}}
	                    <div class="form-group row" wire:ignore>
	                        <label for="guests" class="col-lg-12">{!! ucfirst(trans('textos.invitar_a')) !!}</label>
	                        <div class="col-lg-12">
	                        	<select name="guests[]" class="form-control select2_guests" wire:model.defer="guests" multiple>
		                        	@foreach($users as $u)
		                        		<option value="{{ $u->id }}">{{ $u->name }} {{$u->surname }}</option>
		                        	@endforeach
		                        </select>
	                        </div>
	                    </div>
	                @endif

	                {{-- Productos: --}}
	                @if($products)
	                	<div class="form-group row">
	                        <label for="products_event" class="col-lg-12">{!! ucfirst(trans('textos.productos')) !!}</label>
	                        <div class="col-lg-12" wire:ignore>
	                        	<select name="products_on[]" class="form-control select2_productsEvent" wire:model.defer="products_on" multiple>
		                            @foreach($products as $i => $p)
		                                <option value="{{ $i }}">{{ $p }}</option>    
		                            @endforeach
		                        </select>
	                        </div>
	                    </div>	
	                @endif

	                {{-- Lugar --}}
	                {{-- 24/08/2022: implementar buscador Google maps --}}
                    <div class="form-group row">
                        <label for="place" class="col-lg-12">{!! ucfirst(trans('textos.lugar')) !!}</label>
                        <div class="col-lg-12" wire:ignore>
                        	<x-input type="text" name="place" maxlength="255" wire="true"/>
                        </div>
                    </div>

                    <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                </form>    
            </div>
        </div>
    </div>
</div>
<div wire:ignore.self class="modal fade modal-edit-event" id="modal-edit-event" tabindex="-1" role="dialog" aria-labelledby="editEvent" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editEvent" wire:ignore>
                	<i class="fa fa-calendar-check"></i>
                	{!! ucfirst(trans('textos.evento_editar')) !!} 
                	<u class="text-warning" id="whenEdit"></u>

                    <a href="" class="ml-3 removeEvent btn-confirm" rel="tooltip" title="{!! trans('textos.evento_calendario_eliminar') !!}" data-msg="{!! trans('textos.evento_calendario_confirm_eliminar') !!}"><i class="fa fa-trash"></i></a>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form @if($lw) wire:submit.prevent="update" @else action="{{ route('schedule-events.update') }}" @endif method="post" autocomplete="off" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="eventId" wire:ignore>

                    <div class="form-group row" wire:ignore>
                        <label class="col-lg-12" id="eventAuthor">{!! ucfirst(trans('textos.evento_creado_por')) !!} <b></b></label>
                    </div>

                    {{-- Agenda --}}
                    @if($schedules->count())
                        <div class="form-group row">
                            <label for="schedule_id" class="col-lg-12">{!! ucfirst(trans('textos.agenda')) !!}*</label>
                            <div class="col-lg-12" wire:ignore>
                                <select name="schedule_id" class="form-control select2_scheduleId" id="eventScheduleId" required wire:model.defer="schedule_id">
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
                                <select name="event_type_id" class="form-control select2_eventTypeId" id="eventTypeId" wire:model.defer="event_type_id">
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
                            <x-input type="text" id="eventTitle" name="title" maxlength="255" autocomplete="off" required/>
                        </div>
                    </div>

                    {{-- Descripción --}}
                    <div class="form-group row">
                        <label for="description" class="col-lg-12">{!! ucfirst(trans('textos.descripcion')) !!}</label>
                        <div class="col-lg-12" wire:ignore>
                            <x-input-textarea id="eventDescription" name="description"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Inicio --}}
                        <div class="col-md-6">
                            <div class="row">
                                <label for="start" class="col-lg-12">{!! ucfirst(trans('textos.inicio')) !!}</label>
                                <div class="col-md-8" wire:ignore>
                                    <x-input type="text" name="startDate" class="text-right" id="eventStartDate" maxlength="25" autocomplete="off" required/>
                                </div>
                                <div class="col-md-4" wire:ignore>
                                    <x-input type="text" name="startTime" class="text-right" id="eventStartTime" maxlength="9" autocomplete="off" required/>
                                </div>
                            </div>
                        </div>

                        {{-- Fin --}}
                        <div class="col-md-6">
                            <div class="row">
                                <label for="end" class="col-lg-12">{!! ucfirst(trans('textos.fin')) !!}</label>
                                <div class="col-md-8" wire:ignore>
                                    <x-input type="text" name="endDate" class="text-right" id="eventEndDate" maxlength="25" autocomplete="off" required/>
                                </div>
                                <div class="col-md-4" wire:ignore>
                                    <x-input type="text" name="endTime" class="text-right" id="eventEndTime" maxlength="9" autocomplete="off" required/>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($users->count())
                        {{-- Responsables del evento: --}}
                        <div class="form-group row">
                            <label for="responsible" class="col-lg-12">{!! ucfirst(trans('textos.responsables')) !!}</label>
                            <div class="col-lg-12" wire:ignore>
                                <select name="responsible[]" class="form-control select2_responsible" id="eventResponsible" wire:model.defer="responsible" multiple>
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
                                <select name="guests[]" class="form-control select2_guests" id="eventGuests" wire:model.defer="guests" multiple>
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
                                <select name="products_on[]" class="form-control select2_productsEvent" id="eventProducts" wire:model.defer="products_on" multiple>
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
                            <x-input type="text" name="place" id="eventPlace" maxlength="255"/>
                        </div>
                    </div>

                    <x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
                </form>    
            </div>
        </div>
    </div>
</div>
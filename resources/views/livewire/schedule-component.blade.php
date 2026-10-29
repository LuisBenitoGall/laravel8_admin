<div class="row mb-3">
    {{-- Relación agendas: --}}
    <div class="col-md-2">
        <div class="card mb-3">
            <div class="card-body"> 
                <h3>{!! ucfirst(trans('textos.agendas')) !!}</h3>

                <ul class="schedules-menu">
                    @foreach($schedules as $scd)
                        <li @if($scd->color) style="background-color: {{ $scd->color }}; color:#FFF;" @endif>
                            <label class="custom-chk-rdb mr-1" rel="tooltip" title="{{ ucfirst(trans('textos.agenda_on_off')) }}">
                                <input type="checkbox" wire:model="schedules_on.{{ $scd->id }}" value="{{ $scd->id }}">
                                <span class="chk chksmall"></span>
                            </label>

                            <span>
                                {{ ucfirst($scd->name) }}   
                            </span>
                            
                            @if(!$scd->by_default)
                                <a href="{{ route('schedules.edit', $scd->id) }}" rel="tooltip" title="{{ ucfirst(trans('textos.agenda_editar')) }}">
                                    <i class="fa fa-edit"></i>
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>
                {{-- {{ var_export($schedules_on) }} --}}
            </div>
        </div>
    </div>

    {{-- Calendario --}}
    <div class="col-md-10" wire:ignore>
        <div id="calendario"></div>
    </div>

    {{-- Modals: --}}
    {{-- Add event to calendar --}}
    @include('admin.schedule_events.partials.modal-add-event')

    {{-- Edit event --}}
    @include('admin.schedule_events.partials.modal-edit-event')

    @once
    @push('styles')
    {{-- <link rel="stylesheet" href="{{ asset('plugins/fullcalendar/fullcalendar.min.css') }}"> --}}
    <link rel="stylesheet" href="{{ asset('plugins/fullcalendar/v5/lib/main.min.css') }}">
    @endpush
    @endonce

    @once
    @push('scripts')
    {{-- <script src="{{ asset('plugins/fullcalendar/fullcalendar.min.js') }}"></script>
    <script src="{{ asset('plugins/fullcalendar/locale-all.js') }}"></script>
    <script src="{{ asset('plugins/fullcalendar/gcal.min.js') }}"></script> --}}
 
    <script src="{{ asset('plugins/fullcalendar/v5/moment-with-locales.min.js') }}"></script>
    <script src="{{ asset('plugins/fullcalendar/v5/lib/main.min.js') }}"></script>
    <script src="{{ asset('plugins/fullcalendar/v5/lib/locales-all.min.js') }}"></script> 
    <script src="{{ asset('plugins/fullcalendar/v5/fullcalendar-moment.min.js') }}"></script>

    <script>
    document.addEventListener('livewire:load', function(){
        var Calendar = FullCalendar.Calendar;
        var Draggable = FullCalendar.Draggable;
        var calendarEl = document.getElementById('calendario');
        var checkbox = document.getElementById('drop-remove');
        var data = @this.events;
        moment.locale(idm);
        var calendar = new Calendar(calendarEl, {
            locale: idm,
            //timeZone: 'CEST',
            initialView: 'timeGridWeek',
            weekNumbers: true,
            dayMaxEventRows: 6, // allow "more" link when too many events,
            events: JSON.parse(data),
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
            },
            allDaySlot: true,
            slotMinTime: "06:00:00",
            slotMaxTime: "23:00:00",
            //businessHours: true,
            editable: true,
            navLinks: true,
            selectable: true,
            //selectHelper: true,
            //Crear evento:
            select: function(args){
                var check = moment(args.start, calendar).format('YYYY-MM-DD');
                var today = moment(new Date()).format('YYYY-MM-DD');

                //Bloqueamos días anteriores al actual.
                if(check >= today){
                    var startDate = moment(args.start).format('D/MM/YYYY');
                    var startTime = moment(args.start).format('H:mm');
                    var endDate = moment(args.end).format('D/MM/YYYY');
                    var endTime = moment(args.end).format('H:mm');

                    start_time = moment(args.start).format('dddd, Do MMMM YYYY, H:mm');
                    var mywhen = start_time + ' - ' + endTime;

                    $('#modal-add-event #startDate').val(startDate);
                    @this.set('startDate', startDate);
                    $('#modal-add-event #startTime').val(startTime);
                    @this.set('startTime', startTime);
                    $('#modal-add-event #endDate').val(endDate);
                    @this.set('endDate', endDate);
                    $('#modal-add-event #endTime').val(endTime);
                    @this.set('endTime', endTime);
                    $('#modal-add-event #when').text(mywhen);
                    @this.set('mywhen', mywhen);
                    $('#modal-add-event').modal('toggle');

                }else{
                    return false;
                }
            },
            //Editar evento:
            eventClick: function(info){
                info.jsEvent.preventDefault(); // don't let the browser navigate
                var id = info.event._def.publicId;

                //Limpiamos selector de tipo de evento de calendario:
                $('#eventCeId option[value=""]').prop("selected",true);
                $('.selecthor-values').html('').hide();
                $('.guestsList').val('');

                $.get("/admin/schedule-events/"+id+"/edit", function(response, state){
                    $('.removeEvent').prop('href', '/admin/schedule-events/'+response.id+'/destroy');

                    $('#eventAuthor b').text(response.autor);
                    $('#eventId').val(response.id);
                    //Agenda:
                    $('#eventScheduleId').val(response.schedule_id);
                    $('#eventScheduleId').trigger('change');
                    //Tipo de evento:
                    $('#eventTypeId').val(response.event_type_id);
                    $('#eventTypeId').trigger('change');
                    //Contacto:
                    if(response.contact_user.name){
                        $('#eventContact').val(response.contact_user.name+' '+response.contact_user.surname);
                    }
                    //Título evento:
                    $('#eventTitle').val(response.title);
                    //Descripción:
                    var description = (response.description != null)? response.description:'';
                    $('#eventDescription').text(description);
                    //Fecha:
                    var start = response.inicio.split(" ");
                    $('#eventStartDate').val(start[0]);
                    $('#eventStartTime').val(start[1]);

                    var end = response.fin.split(" ");
                    $('#eventEndDate').val(end[0]);
                    $('#eventEndTime').val(end[1]);

                    endtime = moment(response.end).format('H:mm');
                    starttime = moment(response.start).format('dddd, Do MMMM YYYY, H:mm');
                    var mywhen = starttime+' - '+endtime;
                    $('#whenEdit').text(mywhen);
                    //Responsables:
                    $('#eventResponsible').val(response.responsible);
                    $('#eventResponsible').trigger('change');
                    //Invitados:
                    $('#eventGuests').val(response.guests);
                    $('#eventGuests').trigger('change');
                    //Productos:
                    $('#eventProducts').val(response.products);
                    $('#eventProducts').trigger('change');              
                    //Lugar:
                    $('#eventPlace').val(response.place);
                });
                $('#modal-edit-event').modal('toggle');

                //$('#calendario').fullCalendar('updateEvent', event);
            },
            //Drag & drop:
            eventDrop: info => @this.eventDrop(info.event, info.oldEvent),
            //Modificar horario:
            eventResize: info => @this.eventDrop(info.event, info.oldEvent),
            displayEventTime: false,
            droppable: true, // this allows things to be dropped onto the calendar
            drop: function(info) {
                // is the "remove after drop" checkbox checked?
                if (checkbox.checked) {
                // if so, remove the element from the "Draggable Events" list
                info.draggedEl.parentNode.removeChild(info.draggedEl);
                }
            },

            loading: function(isLoading){
                if(!isLoading){
                    // Reset custom events
                    this.getEvents().forEach(function(e){
                        if (e.source === null) {
                            e.remove();
                        }
                    });
                }
            }
        });

        calendar.render();
        @this.on('refreshCalendar', () => {
            calendar.refetchEvents()
        });

        //Select2:
        $('.select2_scheduleId, .select2_eventTypeId, .select2_responsible, .select2_guests, .select2_productsEvent').select2({
            width: '100%'
        });
        $('.select2_scheduleId').on('change', function(e){
            var schedule_id = $(this).select2("val");
            @this.set('schedule_id', schedule_id);
        });
        $('.select2_eventTypeId').on('change', function(e){
            var event_type_id = $(this).select2("val");
            @this.set('event_type_id', event_type_id);
        });
        $('.select2_responsible').on('change', function(e){
            var responsible = $(this).select2("val");
            @this.set('responsible', responsible);
        });
        $('.select2_guests').on('change', function(e){
            var guests = $(this).select2("val");
            @this.set('guests', guests);
        });
        $('.select2_productsEvent').on('change', function(e){
            var products_on = $(this).select2("val");
            @this.set('products_on', products_on);
        });

        window.initSelect2 = () => {}
        window.livewire.on('select2_scheduleId, select2_eventTypeId, select2_responsible, select2_guests, select2_productsEvent', ()=>{
            select2();
        });

        //Cerrar modal:
        window.livewire.on('closeAddModal', () => {
            $('#modal-add-event').modal('hide');
        });
    });
    </script>
    @endpush
    @endonce
</div>

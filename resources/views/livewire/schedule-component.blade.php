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
                                <input type="checkbox" name="status" @if($scd->status == 1) checked @endif>
                                <span class="chk1 chksmall btnActiveSchedule" data-id="{{ $scd->id }}"></span>
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
            </div>
        </div>
    </div>

    {{-- Calendario --}}
    <div class="col-md-10">
        <div id="calendario"></div>
    </div>

    {{-- Modals: --}}
    {{-- Add event to calendar: --}}
    @include('admin.schedule_events.partials.modal-add-event')

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
    <script src="{{ asset('plugins/fullcalendar/v5/lib/main.min.js') }}"></script>
    <script src="{{ asset('plugins/fullcalendar/v5/lib/locales-all.min.js') }}"></script> 
    {{-- <script src="{{ asset('js/custom-fullcalendar.js') }}"></script> --}}

    <script>
    document.addEventListener('livewire:load', function(){
        var Calendar = FullCalendar.Calendar;
        var Draggable = FullCalendar.Draggable;
        var calendarEl = document.getElementById('calendario');
        var checkbox = document.getElementById('drop-remove');
        var data = @this.events;
        var calendar = new Calendar(calendarEl, {
            locale: idm,
            //timeZone: 'CEST',
            initialView: 'timeGridWeek',
            weekNumbers: true,
            events: JSON.parse(data),
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
            },
            allDaySlot: true,
            slotMinTime: "06:00:00",
            slotMaxTime: "23:00:00",
            editable: true,
            selectable: true,
            //selectHelper: true,




        dateClick(info){
               var title = prompt('Enter Event Title');
               var date = new Date(info.dateStr + 'T00:00:00');
               if(title != null && title != ''){
                 calendar.addEvent({
                    title: title,
                    start: date,
                    allDay: true
                  });
                 var eventAdd = {title: title,start: date};
                 @this.addevent(eventAdd);
                 alert('Great. Now, update your database...');
               }else{
                alert('Event Title Is Required');
               }
        },
            
            displayEventTime: false,
            droppable: true, // this allows things to be dropped onto the calendar
            drop: function(info) {
                // is the "remove after drop" checkbox checked?
                if (checkbox.checked) {
                // if so, remove the element from the "Draggable Events" list
                info.draggedEl.parentNode.removeChild(info.draggedEl);
                }
            },
            eventDrop: info => @this.eventDrop(info.event, info.oldEvent),
            loading: function(isLoading) {
                    if (!isLoading) {
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
    });
    </script>
    @endpush
    @endonce
</div>

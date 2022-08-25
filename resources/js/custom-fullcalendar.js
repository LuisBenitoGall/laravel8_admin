function Calendario(){
	//Inicializar calendario:
	var date = new Date();
	var d = date.getDate();
    m = date.getMonth();
    y = date.getFullYear();

    var events = [];
    $.ajax({
    	url: '/admin/schedule-events',
    	type: "GET",
    	dataType: "JSON",
    	async: false
    }).done(function(data){
    	events = data;
    });

	$('#calendario').fullCalendar({
		locale: idm,
		//timeZone: 'CEST',
		defaultView: 'agendaWeek',
		weekNumbers: true,
		//googleCalendarApiKey: 'AIzaSyAUl4T-gtIrLiTVln2j2vfsKlgC3CNbGNs',
		//googleCalendarApiKey: 'AIzaSyDcnW6WejpTOCffshGDDb4neIrXVUA1EAE',
		//googleCalendarApiKey: '4cc48935e52c9eee3063f573efe4ca1e121d846b',
		//googleCalendarApiKey: 'AIzaSyAGBcK02fDqRpi_ETS7KSfjZXVMPhhmk24',
		//googleCalendarApiKey: 'AIzaSyAGBcK02fDqRpi_ETS7KSfjZXVMPhhmk24',
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay,listWeek'
		},
		allDaySlot: true,
		minTime: "08:00:00",
		maxTime: "23:00:00",
		selectable: true,
		selectHelper: true,
		//Crear evento:
		select: function(start, end, allDay){
			var check = $.fullCalendar.moment(start).format('YYYY-MM-DD');
			var today = $.fullCalendar.moment(new Date()).format('YYYY-MM-DD');
			$('.selecthor-values').html('').hide();
			$('.selecthor option[value=""]').prop('selected', true);
			$('.guestsList').val('');

			//Bloqueamos días anteriores al actual.
			if(check >= today){
				endtime = $.fullCalendar.moment(end).format('H:mm');
	            starttime = $.fullCalendar.moment(start).format('dddd, Do MMMM YYYY, H:mm');
	            var mywhen = starttime + ' - ' + endtime;
	            start = moment(start).format('D/MM/YYYY H:mm');
	            end = moment(end).format('D/MM/YYYY H:mm');
	            allDay = $.fullCalendar.moment(allDay).format('dddd, Do MMMM YYYY, H:mm');

	            $('#modal-add-event #startTime').val(start);
	            $('#modal-add-event #endTime').val(end);
	            $('#modal-add-event #when').text(mywhen);
	            //$('#modal-add-event #when').text(allDay);

				$('#modal-add-event').modal('toggle');
				$('.guestsList').val('');
				
				$('#calendario').fullCalendar('unselect');
			
			}else{
				return false;
			}
		},
		editable: true,
		//Editar evento:
		eventClick: function(event, element){
			var id = event.id;

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
				//Título evento:
				$('#eventTitle').val(response.title);
				//Descripción:
				var description = (response.description != null)? response.description:'';
				$('#eventDescription').text(description);
				//Fecha:
				$('#eventStart').val(response.inicio);
				$('#eventEnd').val(response.fin);
				endtime = $.fullCalendar.moment(response.end).format('H:mm');
	            starttime = $.fullCalendar.moment(response.start).format('dddd, Do MMMM YYYY, H:mm');
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

    		$('#calendario').fullCalendar('updateEvent', event);
		},
		//Drag & drop:
		eventDrop: function(event, delta, revertFunc, jsEvent, ui, view){ 
			var id = event.id;
			var start = moment(event.start).format();
			start = start.replace('T', ' ');
			var end = moment(event.end).format();
			end = end.replace('T', ' ');
			
            $.ajax({
           		headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
              	url: '/admin/schedule-events/drop-event',
              	data: 'id='+id+'&start='+start+'&end='+end,
              	type: "POST"
            });
        },
        //Modificar horario:
        eventResize: function(event){  // resize to increase or decrease time of event
        	var id = event.id;
			var start = moment(event.start).format();
			start = start.replace('T', ' ');
			var end = moment(event.end).format();
			end = end.replace('T', ' ');
			
            $.ajax({
           		headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
              	url: '/admin/schedule-events/drop-event',
              	data: 'id='+id+'&start='+start+'&end='+end,
              	type: "POST"
            });
        },
		eventLimit: true, // allow "more" link when too many events,
		events: events,

		/*events: {
      		googleCalendarId: 'lbenito@adncomunicacio.com',
      		className: 'gcal-event' 
    	}, */ 
    	//events: 'en.usa#holiday@group.v.calendar.google.com', 
    	//events: 'adn-comunicacio@adn-comunicacio-admin.iam.gserviceaccount.com',
    	//event: 'adncomunicacio.com_bnpnoclusd757i2jhkst8o49go@group.calendar.google.com',
    	//events: 'adncomunicacio.com_c78bbuclraqn2mic0h8pu9evos@group.calendar.google.com',
		droppable: true, // this allows things to be dropped onto the calendar
		drop: function(){
			if($('#drop-remove').is(':checked')){
				// if so, remove the element from the "Draggable Events" list
				$(this).remove();
			}
		}
	});	
}

$(document).ready(function(){
	Calendario();
});
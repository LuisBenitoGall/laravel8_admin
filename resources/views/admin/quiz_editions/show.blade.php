@extends('admin.partials.layout')

@section('content')

<div class="row justify-content-md-center mb-3 pt-5" id="quiz"></div>

@push('scripts')
<script>
const questions = @json($questions);

//render question:
function renderHtml(obj, idm = 'es'){
	//console.log(obj);
	var html = '<div class="col-lg-5 question-image mb-5">';
		html += '<img src="/img/questions/'+obj.image+'">';
		html += '<div class="spin">';
			html += '<svg width="180" height="180" xmlns="https://www.w3.org/2000/svg"><g><title>spin</title>';
				html += '<circle id="circle" class="circle_animation" r="69.85699" cy="81" cx="81" stroke-width="5" stroke="#FFF" fill="none"/></g>';
			html += '</svg>';
			html += '<div id="countdown"></div>';
		html += '</div>';
	html += '</div>';

	html += '<div class="col-lg-7 question-text mb-5">';
		var points_text = obj.points == 1? txtPunto:txtPuntos;
		html += '<p class="text-center text-warning mb-0"><b><i>Por '+obj.points+' '+points_text+'</i></b></p>';
		html += '<h4>'+obj.question_+'</h4>';

		html += '<div class="question-answers mt-4">';
			for(i=0; i<obj.options_.length; i++){
				html += '<div class="option" data-id="'+[i]+'">'+obj.options_[i]+'</div>';	
			}
		html += '</div>';

		html += '<div class="question-link"></div>';
		html += '<input type="hidden" id="answer">';

		html += '<div class="text-center mt-5">';
			html += '<button class="btn btn-info btn-lg mx-2 btnCheck" data-questionid="'+obj.id+'">'+txtComprobar+' <i class="fa fa-check"></i></button>';
			html += '<button class="btn btn-warning btn-lg mx-2 btnNextQuestion">'+txtSiguiente+' <i class="fa fa-chevron-right"></i></button>';
			html += '<a class="btn btn-danger btn-lg mx-2 btnSalir" href="/quiz/resultados/'+obj.quiz_id+'">'+txtSalir+'  <i class="fa fa-sign-out-alt"></i></a>';
		html += '</div>';
	html += '</div>';

	return html;	
}

var initialOffset = '440';
var totalTime = {{ $edition->time }};
var cancelarTime = false;
var checkQuestion = true;
var k = 1;

$('.circle_animation').css('stroke-dashoffset', initialOffset-((k+1)*(initialOffset/totalTime)));

//Guardar respuesta:
function saveAnswer(question, answer, points){
	$.ajax({
        url: "/admin/quiz-edition-answers/store",
        type: "post",
        dataType: "json",
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            edition: {{ $edition->id }},
            question: question,
            answer: answer,
            points: points
        },
        success: function(response){
        	$('.option[data-id="'+response.ok_answer+'"]').addClass('ok-answer');
	   		//Respuesta del usuario:
	   		if(response.ok_answer == response.your_answer){
	   			var icono = '<span class="fa fa-check"></span>';
	   		}else{
	   			var icono = '<span class="fa fa-times"></span>';
	   		}
	   		$('.option[data-id="'+response.your_answer+'"]').append(icono);

	   		//Video:
	   		if(response.video != null){
	   			$('.question-link').html('<a target="_blank" href="'+response.video+'">'+txtVerVideoAqui+'</a>');	
	   		}
        }
    });


	// $.get("/quiz/"+question+"/"+answer+"/solve", function(response, state){
   	// 	//Marcamos respuesta correcta:
   	// 	$('.option[data-id="'+response.ok_answer+'"]').addClass('ok-answer');
   	// 	//Respuesta del usuario:
   	// 	if(response.ok_answer == response.your_answer){
   	// 		var icono = '<span class="fa fa-check"></span>';
   	// 	}else{
   	// 		var icono = '<span class="fa fa-times"></span>';
   	// 	}
   	// 	$('.option[data-id="'+response.your_answer+'"]').append(icono);

   	// 	//Video:
   	// 	if(response.video != null){
   	// 		$('.question-link').html('<a target="_blank" href="'+response.video+'">'+txtVerVideoAqui+'</a>');	
   	// 	}
	// });
}

//Countdown:
function updateClock(){
	if(!cancelarTime){
		document.getElementById('countdown').innerHTML = totalTime;

		//Tiempo finalizado:
		if(totalTime<1){
			var question = $('.btnCheck').data('questionid');
			var points = 0;
			var answer = 999999;

			$('.btnCheck').fadeOut(function(){
				$('.btnNextQuestion, .btnSalir').fadeIn();	
			});
			$('.question-link').append('<p class="text-danger pt-3"><b>'+txtTiempoAgotado+'.</b></p>');
			//Guardamos respuesta:
			saveAnswer(question, answer, points);

		}else{
			$('#countdown').text(totalTime);
			totalTime-=1;
			k++;
			setTimeout("updateClock()",1000);
		}
	}
}

$(document).ready(function(){
	var respondidas = [];
	var contador = 0;

	if(contador == 0){
		var firstQuestion = questions[0];

		var html = renderHtml(firstQuestion);

		$('#quiz').html(html);
		//$('.btnNextQuestion').prop('disabled', true);
		$('.btnNextQuestion, .btnSalir').hide();
		$('.btnCheck').prop('disabled', true);
		updateClock();
		
		respondidas.push(firstQuestion.id);	
		contador++;
	}

	//Seleccionar respuesta:
	$(document).on('click', '.option', function(){
		var id = $(this).data('id');
		$('#answer').val(id);
		$('.option').removeClass('selected');
		$(this).addClass('selected');
		$('.btnCheck').prop('disabled', false);
	});

	//Comprobar respuesta:
	$(document).on('click', '.btnCheck', function(e){
		e.preventDefault();
		if(checkQuestion){
			$('.btnCheck').fadeOut(function(){
				$('.btnNextQuestion, .btnSalir').fadeIn();	
			});
			var question = $(this).data('questionid');
			var answer = $('#answer').val();

			//Cancelamos la cuenta atrás:
			cancelarTime = true;

			//Guardamos respuesta:
			saveAnswer(question, answer);

			checkQuestion = false;
		}
	});

	//Siguiente pregunta:
	$(document).on('click', '.btnNextQuestion', function(){
		var question = questions[contador];
		cancelarTime = false;
		totalTime = {{ $edition->time }};

		if(typeof question != 'undefined'){
			var html = renderHtml(question);
			$('#question').html(html);
			$('.btnCheck').prop('disabled', true);
			updateClock();
			respondidas.push(question.id);	
			contador++;

		//Preguntas acabadas:
		}else{
			window.location.href = '/admin/quiz-editions/'+{{ $edition->id }}+'/ranking';	
		}
	});
});
</script>
@endpush

@endsection
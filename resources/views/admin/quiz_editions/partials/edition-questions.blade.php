<div class="form-group row">
	@if($disabled != 1)
		{{-- Marcar / desmarcar todas las preguntas --}}
		<label class="text-warning col-md-4 col-form-label">{{ trans('textos.preguntas_on_off_todas') }}</label>

		<div class="col-md-2 pt-1">
			<label class="switch">
	            <input type="checkbox" class="btnSetEditionQuestions" @if($active_questions) checked @endif>
	            <span class="slider round"></span>
	        </label>
		</div>

		{{-- Puntuación de las preguntas --}}
		<label class="text-warning col-md-3 col-form-label">{{ trans('textos.preguntas_puntos_todas') }}</label>
		<div class="col-md-2 mb-5">
			<x-input type="text" class="text-right setPointsAll" name="points" autocomplete="off" maxlength="9"/>
		</div>
	@endif

	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblQuizEditionQuestions">
			<thead>
				<tr>
					<th class="wdt50 text-center">{!! ucfirst(trans('textos.orden')) !!}</th>
					<th class="wdt200">{!! ucfirst(trans('textos.titulo')) !!}</th>
					<th class="wdt300">{!! ucfirst(trans('textos.cuestion')) !!}</th>
					<th class="wdt100">{!! ucfirst(trans('textos.tipo')) !!}</th>
					<th class="wdt50 text-center">{!! ucfirst(trans('textos.puntos')) !!}</th>
					<th class="wdt100 text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>
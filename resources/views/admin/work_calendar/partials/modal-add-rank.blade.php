<div class="modal fade modal-add-rank" id="modal-add-rank" tabindex="-1" role="dialog" aria-labelledby="addRank" aria-hidden="true">
	<div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addRank">{!! ucfirst(trans('textos.rango_fechas')) !!} {{ $year }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
            	<p class="text-warning">{!! trans('textos.rango_fechas_texto') !!}</p>
            	
            	<form method="post" autocomplete="off" action="{{ route('work-calendar.set-ranks') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}	
					<input type="hidden" name="workplace_id" value="{{ $workplace->id }}">
					<input type="hidden" name="year" value="{{ $year }}">

					{{-- Tipo de jornada --}}
					<div class="form-group row">
						<label for="day_type_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.jornada')) !!}*</label>
						<div class="col-lg-10">
							<x-select name="day_type_id" class="select2" label="tipo_jornada_selec" :options="$day_types" required/>
						</div>
					</div>
							
					{{-- Desde --}}
					<div class="form-group row">
						<label for="from" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.desde')) !!}</label>
						<div class="col-lg-10">
							<x-date-picker name="from" :format="$languages[config('app.locale')][4]"/>
						</div>	
					</div>

					{{-- Hasta --}}
					<div class="form-group row">
						<label for="to" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.hasta')) !!}</label>
						<div class="col-lg-10">
							<x-date-picker name="to" :format="$languages[config('app.locale')][4]"/>
						</div>
					</div>

					<x-btn-submit name="submit_rank" label="{{ trans('textos.guardar') }}"/>
                </form>
            </div>
        </div>
    </div>
</div>	
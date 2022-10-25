<div class="modal fade modal-custom-timetables" id="modal-custom-timetables" tabindex="-1" role="dialog" aria-labelledby="customTimetables" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="customTimetables">{!! ucfirst(trans('textos.horarios_personalizados')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('user-timetables.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
					<input type="hidden" name="user_id" id="userId" value="{{ $user->id }}">
					<input type="hidden" name="employee_id" id="employeeId">
					<input type="hidden" name="company_id" id="companyId">
					<input type="hidden" name="ut_id" id="utId">

					<div class="form-group row">
						{{-- Fecha inicio --}}
						<div class="col-6 col-md-4 col-lg-3">
							<div class="row">
								<label for="from" class="col-lg-12">{!! ucfirst(trans('textos.desde')) !!} *</label>
		                        <div class="col-lg-12">
		                        	<x-date-picker name="from" :format="$languages[config('app.locale')][4]"/>
		                        </div>	
							</div>
						</div>
                        
                        {{-- Fecha fin --}}
                        <div class="col-6 col-md-4 col-lg-3">
							<div class="row">
								<label for="to" class="col-lg-12">{!! ucfirst(trans('textos.hasta')) !!}</label>
		                        <div class="col-lg-12">
		                        	<x-date-picker name="to" :format="$languages[config('app.locale')][4]"/>
		                        </div>	
							</div>
						</div>
                    </div>

                    <div class="form-group row">
                        <label for="daily_hours" class="col-lg-12">{!! ucfirst(trans('textos.horas_dia')) !!}</label>
                        <div class="col-lg-12">
							<div class="row">
								{{-- Lunes --}}
								<label for="jornada" class="col-md-3 col-form-label mb-3">{!! ucfirst(trans('textos.lunes')) !!}</label>	
								<div class="col-md-2 mb-3">
									<input type="text" value="" name="jornada_1" id="jornada_1" class="form-control text-right maskHours jornada">
								</div>
								<div class="col-md-1">
									<a href="#" class="btn btn-sm btn-warning mt-1 btnDayType" data-num="1" rel="tooltip" title="{{ trans('textos.tipo_jornada') }}">
										<i class="fa fa-list-alt"></i>
									</a>
								</div>	
								<input type="hidden" name="day_type_1" id="dayType_1">

								{{-- Martes --}}
								<label for="jornada" class="col-md-3 col-form-label mb-3">{!! ucfirst(trans('textos.martes')) !!}</label>	
								<div class="col-md-2 mb-3">
									<input type="text" value="" name="jornada_2" id="jornada_2" class="form-control text-right maskHours jornada">
								</div>
								<div class="col-md-1">
									<a href="#" class="btn btn-sm btn-warning mt-1 btnDayType" data-num="2" rel="tooltip" title="{{ trans('textos.tipo_jornada') }}">
										<i class="fa fa-list-alt"></i>
									</a>
								</div>	
								<input type="hidden" name="day_type_2" id="dayType_2">

								{{-- Miércoles --}}
								<label for="jornada" class="col-md-3 col-form-label mb-3">{!! ucfirst(trans('textos.miercoles')) !!}</label>	
								<div class="col-md-2 mb-3">
									<input type="text" value="" name="jornada_3" id="jornada_3" class="form-control text-right maskHours jornada">
								</div>
								<div class="col-md-1">
									<a href="#" class="btn btn-sm btn-warning mt-1 btnDayType" data-num="3" rel="tooltip" title="{{ trans('textos.tipo_jornada') }}">
										<i class="fa fa-list-alt"></i>
									</a>
								</div>	
								<input type="hidden" name="day_type_3" id="dayType_3">

								{{-- Jueves --}}
								<label for="jornada" class="col-md-3 col-form-label mb-3">{!! ucfirst(trans('textos.jueves')) !!}</label>	
								<div class="col-md-2 mb-3">
									<input type="text" value="" name="jornada_4" id="jornada_4" class="form-control text-right maskHours jornada">
								</div>
								<div class="col-md-1">
									<a href="#" class="btn btn-sm btn-warning mt-1 btnDayType" data-num="4" rel="tooltip" title="{{ trans('textos.tipo_jornada') }}">
										<i class="fa fa-list-alt"></i>
									</a>
								</div>	
								<input type="hidden" name="day_type_4" id="dayType_4">

								{{-- Viernes --}}
								<label for="jornada" class="col-md-3 col-form-label mb-3">{!! ucfirst(trans('textos.viernes')) !!}</label>	
								<div class="col-md-2 mb-3">
									<input type="text" value="" name="jornada_5" id="jornada_5" class="form-control text-right maskHours jornada">
								</div>
								<div class="col-md-1">
									<a href="#" class="btn btn-sm btn-warning mt-1 btnDayType" data-num="5" rel="tooltip" title="{{ trans('textos.tipo_jornada') }}">
										<i class="fa fa-list-alt"></i>
									</a>
								</div>	
								<input type="hidden" name="day_type_5" id="dayType_5">

								{{-- Sábado --}}
								<label for="jornada" class="col-md-3 col-form-label mb-3">{!! ucfirst(trans('textos.sabado')) !!}</label>	
								<div class="col-md-2 mb-3">
									<input type="text" value="" name="jornada_6" id="jornada_6" class="form-control text-right maskHours jornada">
								</div>
								<div class="col-md-1">
									<a href="#" class="btn btn-sm btn-warning mt-1 btnDayType" data-num="6" rel="tooltip" title="{{ trans('textos.tipo_jornada') }}">
										<i class="fa fa-list-alt"></i>
									</a>
								</div>	
								<input type="hidden" name="day_type_6" id="dayType_6">

								{{-- Domingo --}}
								<label for="jornada" class="col-md-3 col-form-label mb-3">{!! ucfirst(trans('textos.domingo')) !!}</label>	
								<div class="col-md-2 mb-3">
									<input type="text" value="" name="jornada_7" id="jornada_7" class="form-control text-right maskHours jornada">
								</div>
								<div class="col-md-1">
									<a href="#" class="btn btn-sm btn-warning mt-1 btnDayType" data-num="7" rel="tooltip" title="{{ trans('textos.tipo_jornada') }}">
										<i class="fa fa-list-alt"></i>
									</a>
								</div>	
								<input type="hidden" name="day_type_7" id="dayType_7">
							</div>		
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-12 text-right">
                            <input type="submit" name="submit-save" class="btn btn-info" value="{!! ucfirst(trans('textos.guardar')) !!}">
                        </div>
                    </div>
                </form>  

                <table class="modal-table" id="tblCustomTimetables">
                	<hr>
                    <thead>
                        <tr>
                        	<th class="text-center">{{ ucfirst(trans('textos.desde')) }}</th>
                        	<th class="text-center">{{ ucfirst(trans('textos.hasta')) }}</th>
                        	<th>{{ ucfirst(trans('textos.horas')) }}</th>
                        	<th class="text-center">{{ ucfirst(trans('textos.acciones')) }}</th>
                        </tr>
                    </thead>

                    <tbody>
                    	
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
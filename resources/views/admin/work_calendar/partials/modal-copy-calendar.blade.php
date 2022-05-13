<div class="modal fade modal-copy-working-hours" id="modal-copy-working-hours" tabindex="-1" role="dialog" aria-labelledby="copyWorkingHours" aria-hidden="true">
	<div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="copyWorkingHours">{!! ucfirst(trans('textos.calendario_copiar')) !!} {{ $year }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
            	<form method="post" autocomplete="off" action="{{ route('work-calendar.copy') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}	
					<input type="hidden" name="workplace_id" value="{{ $workplace->id }}">
					<input type="hidden" name="year" value="{{ $year }}">

					{{-- Copiar a otras empresas --}}
					@if(count(session('companies')) > 1)
						<div class="row mt-4">
							<label for="companies" class="col-lg-12">{!! ucfirst(trans('textos.copiar_otras_empresas')) !!}</label>	
							<div class="col-lg-12">	
								@php
								$companies_ = [];
								foreach(session('companies') as $c){
									if($c->id != session('currentCompany')){
										$companies_[$c->id] = $c->name;
									}
								}
								@endphp

								<x-select name="companies[]" class="select2" multiple="true" :options="$companies_"/>
	                        </div>
	                    </div>
                    @endif

					{{-- Copiar a otros centros de trabajo --}}
					@if($workplaces->count() > 1)
						<div class="row mt-4">
							<label for="workplaces" class="col-lg-12">{!! ucfirst(trans('textos.copiar_otros_centros_trabajo')) !!}</label>	
							<div class="col-lg-12">
								@php
                                $workplaces__ = [];
                                foreach($workplaces as $wp){
                                    //Descontamos el centro de trabajo seleccionado:
                                    if($wp->id != $workplace->id){
                                        $name = $wp->Town? $wp->name.' - '.$wp->Town:$wp->name;
                                        $workplaces__[$wp->id] = $name;
                                    }
                                }
                                @endphp

                                <x-select name="workplaces[]" class="select2" multiple="true" :options="$workplaces__"/>
							</div>
						</div>
					@endif	

					<x-btn-submit name="submit_copy" label="{{ trans('textos.guardar') }}"/>
                </form>
            </div>
        </div>
    </div>
</div>	
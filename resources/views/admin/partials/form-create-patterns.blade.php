<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route($ruta) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						<div class="col-md-10 offset-md-2">
							<p class="text-warning">{!! trans('textos.'.$info_text) !!}</p>
						</div>

						{{-- Nombre patrón --}}
				        <label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.patron')) !!} *</label>
				        <div class="col-lg-4">
				        	<x-input type="text" name="name" autocomplete="off" maxlength="255" required/>
				        </div>

				        {{-- Número de dígitos --}}
				        <label for="ndigits" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.digitos_numero')) !!} *</label>
				        <div class="col-lg-1">
				        	<x-input type="number" name="ndigits" autocomplete="off" value="1" min="1" max="20" required/>
				        </div>

				        {{-- Estado --}}
						<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.patron_activo')) !!}</label>
						<div class="col-lg-1">
							<label class="custom-chk-rdb">
						        <input type="checkbox" name="status" checked>
						        <span class="chk" style="top: 5px;"></span>
						    </label>  
						</div>
				    </div>

				    @if($yearly_reset)
				    	<div class="form-group row">
				    		<label for="yearly_reset" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.reset_anual')) !!}</label>
						    <div class="col-lg-1 pt-1">
						        <label class="custom-chk-rdb">
						        	<input type="checkbox" name="yearly_reset" checked>
						        	<span class="chk"></span>
						    	</label>

						    	<div class="position-absolute" style="left: 65px; top: 0;">
				            		{!! App\Helpers\helpers::info('pattern-yearly-reset') !!}
				            	</div>
						    </div>	
				    	</div>
				    @endif

					<div class="form-group row mt-4">
						{{-- Añadir segmento --}}
						<label for="" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.segmento_add')) !!}</label>
				        <div class="col-lg-1">
							<button class="btn btn-warning btn-circle btnAddSegment" rel="tooltip" title="{{ trans('textos.segmento_add') }}">
								<i class="fa fa-plus"></i>
							</button>
				        </div>

				        <div class="col-md-9">
							<p class="text-warning">
								{!! trans('textos.patron_condiciones_texto') !!}
							</p>
						</div>
						
						<label for="segments" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.segmentos')) !!}</label>
						<div class="col-md-8">
				            <div class="segments">
								<div class="row mb-2 segment">
									{{-- Tipo de segmento --}}
						            <div class="col-6">
						            	@php
						            	$options = [
						            		'digits' => ucfirst(trans('textos.digitos')),
						            		'text' => ucfirst(trans('textos.texto')),
						            		'year' => ucfirst(trans('textos.any'))
						            	];
						            	@endphp

						            	<x-select name="segment[]" class="segment-options" label="segmento_tipo" :options="$options"/>
						            </div>

						            {{-- Valor del segmento --}}
						            <div class="col-5">
						            	<x-input type="text" name="segment_text[]" class="segment_text" autocomplete="off" disabled/>
						            </div>

						            {{-- Eliminar segmento --}}
						            <div class="col-1 pt-1">
						            	<button class="btn btn-danger btn-circle btn-circle-mini btnRemoveSegment">
											<i class="fa fa-times"></i>
										</button>
						            </div>
						        </div>
				            </div>
				        </div>
				    </div>

					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}" id="submitPattern" disabled/>
				</form>
			</div>
		</div>
	</div>
</div>

{{-- HTML para clonar y añadir a segments --}}
<div class="clones" style="display: none;">
	<div class="row mb-2 segment">
        <div class="col-6">
        	<x-select name="segment[]" class="segment-options" label="segmento_tipo" :options="$options"/>
        </div>
        <div class="col-5">
        	<x-input type="text" name="segment_text[]" class="segment_text" autocomplete="off" disabled/>
        </div>
        <div class="col-1 pt-1">
            <button class="btn btn-danger btn-circle btn-circle-mini btnRemoveSegment">
				<i class="fa fa-times"></i>
			</button>
        </div>
    </div>
</div>
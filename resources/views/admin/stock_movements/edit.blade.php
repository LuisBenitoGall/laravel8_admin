@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Listado movimientos --}}
		<a href="{{ route('stock-movements.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_movimientos_stock') !!}
		</a>

		{{-- Nuevo movimiento --}}
		@can('stock-movements.create')
			<a class="btn btn-info" href="{{ route('stock-movements.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.movimiento_nuevo')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-xs-12 col-sm-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('stock-movements.update', $movement) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Nombre movimiento --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.movimiento')) !!}*</label>
                        <div class="col-lg-6">
                        	<x-input type="text" name="name" placeholder="{!! ucfirst(trans('textos.movimiento')) !!}" autocomplete="off" maxlength="75" required value="{{ $movement->name }}"/>
                        </div>

                        {{-- Siglas --}}
                        <label for="acronym" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.siglas')) !!}*</label>
                        <div class="col-lg-2">
                        	<x-input type="text" name="acronym" placeholder="{!! ucfirst(trans('textos.siglas')) !!}" autocomplete="off" maxlength="3" required value="{{ $movement->acronym }}"/>
                        </div>
                    </div>

                    <div class="form-group row">
                    	{{-- Signo --}}
                        <label for="sign" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.signo')) !!}</label>
                        <div class="col-lg-10">
                        	<p style="margin-bottom: 0; padding-top: 6px;"><b>{!! ucfirst(trans('textos.'.$signs[$movement->sign])) !!}</b></p>
                        </div>
                    </div>
                    <br>

                    {{-- Tabs idiomas: --}}
					<x-tabs-languages :languages="$languages" />

					<div class="tab-content pt-5">
						@php
						$i = 0;
						$explanations = $movement->explanation? unserialize($movement->explanation):false;
						@endphp

						@foreach($languages as $lang)	
							@php
							$explanation = $explanations? $explanations[$lang[0]]:'';
							@endphp

							<div class="tab-pane fade show @if($i == 0) active @endif" role="tabpanel" id="tab-{{ $lang[0] }}">
								{{-- Explicación --}}
								<div class="form-group row">
									<label for="explanation" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.explicacion')) !!} {{ $lang[3] }}</label>
									<div class="col-lg-10">
										<x-input-textarea name="explanation_{{ $lang[0] }}" class="editor" value="{!! $explanation !!}"/>
									</div>
								</div>
							</div>

							@php
							$i++;
							@endphp
						@endforeach
					</div>

                    <x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>	
	</div>
</div>

@endsection
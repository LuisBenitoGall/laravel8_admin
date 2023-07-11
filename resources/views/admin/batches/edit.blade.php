@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones --}}
	<div class="col-12">
		{{-- Lotes --}}
		@can('batches.index')
			<a href="{{ route('batches.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_lotes') !!}
			</a>
		@endcan

		{{-- Nuevo lote --}}
		@can('batches.create')
			<a class="btn btn-info" href="{{ route('batches.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.lote_nuevo')) !!}</a>
		@endcan

		{{-- Orden de trabajo --}}
		@if($work_order)
			@php
			$wo_num = $work_order->work_order_num? $work_order->work_order_num:($work_order->work_order_old_num? $work_order->work_order_old_num:$work_order->id);
			@endphp

			<a href="{{ route('work-orders.edit', $work_order) }}" class="btn btn-info">
				<i class="fa fa-industry"></i>
				{!! trans('textos.trabajo_orden') !!} {{ $wo_num }}
			</a>	
		@endif

		{{-- Eliminar lote --}}
		@can('batches.destroy')
			<form method="POST" action="{{ route('batches.destroy', $batch->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
    			<input name="_method" type="hidden" value="DELETE">
    			{{ csrf_field() }}
    			<button type="submit" class="btn btn-danger frm-confirm" rel="tooltip" title="{!! ucfirst(trans('textos.lote_eliminar')) !!}" data-msg="{!! ucfirst(trans('textos.lote_eliminar_confirm')) !!}"><i class="fa fa-trash"></i></button>
			</form>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-3">
					{{ ucfirst(trans('textos.lote')) }} <u>{{ $batch->batch_num }}</u>, 
					{{ ucfirst(trans('textos.producto')) }} 
					<u><a target="_blank" href="{{ route('products.show', $product->id) }}">{{ $product->name }}</a></u>
				</h3>

				{{-- Info --}}
				<div class="mb-3">
					{{-- Fecha alta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.fecha_alta')) !!}:
						<b>
							{{ $batch->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Autor --}}
					@if($created_by)
						<label class="mr-5">
							{!! ucfirst(trans('textos.creado_por')) !!}:
							<b>{{ ucfirst($created_by->name) }} {{ ucfirst($created_by->surname) }}</b>
						</label>
					@endif

					{{-- Fecha última actualización --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.ultima_edicion')) !!}:
						<b>
							@if($batch->updated_at) {{ $batch->updated_at->format($languages[session()->get('locale')][4].' H:i:s') }} @endif
						</b>
					</label>

					{{-- Actualizado por --}}
					@if($updated_by)
						<label class="mr-5">
							{!! ucfirst(trans('textos.actualizado_por')) !!}:
							<b>{{ ucfirst($updated_by->name) }} {{ ucfirst($updated_by->surname) }}</b>
						</label>
					@endif
				</div>

				<form method="post" autocomplete="off" action="{{ route('batches.update', $batch) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Número lote: si proviene de patrón no se permite modificarlo. --}}
						<label for="batch_num" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.lote_numero')) !!}*</label>
						<div class="col-lg-4">
							@if($batch->pattern_id)
								<x-input type="text" name="batch_num" value="{{ $batch->batch_num }}" readonly/>
							@else
								<x-input type="text" name="batch_num" placeholder="{!! ucfirst(trans('textos.lote')) !!}" autocomplete="off" maxlength="150" required value="{{ $batch->batch_num }}"/>
							@endif						
						</div>	

						{{-- Cantidad --}}
						@if($batch->initial_qty > 0)
							<label for="initial_qty" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cantidad_inicial')) !!}</label>
							<div class="col-lg-2">
								<x-input type="text" name="initial_qty" class="text-right" autocomplete="off" maxlength="9" value="{{ $batch->initial_qty }}" disabled/>
							</div>
						@endif
					</div>

					<div class="form-group row">
						{{-- Fecha cierre lote --}}
						<label for="deadline" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.lote_fecha_cierre')) !!}</label>
						<div class="col-md-3">
							@php
							$deadline = $batch->deadline != ''? \Carbon\Carbon::parse($batch->deadline)->format($languages[session()->get('locale')][4]):'';
							@endphp

							<x-date-picker name="deadline" id="deadline" value="{{ $deadline }}" :format="$languages[config('app.locale')][4]"/>
						</div>

						{{-- Fecha de caducidad --}}
						<label for="expiry_date" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.fecha_caducidad')) !!}</label>
						<div class="col-md-3">
							@php
							$expiry_date = $batch->expiry_date != '' && $batch->expiry_date != config('constants.UNDEFINED_DATE_')? \Carbon\Carbon::parse($batch->expiry_date)->format($languages[session()->get('locale')][4]):'';
							@endphp

							<x-date-picker name="expiry_date" id="expiry_date" value="{{ $expiry_date }}" :format="$languages[config('app.locale')][4]"/>
						</div>
					</div>

					<div class="form-group row">
                        {{-- Nº documento --}}
                        <label for="doc_num" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.documento_num')) !!}</label>
                        <div class="col-lg-4">
                            <x-input type="text" name="doc_num" autocomplete="off" maxlength="100" value="{{ $batch->doc_num }}"/>
                        </div>

                        {{-- Documento --}}
                        <label for="doc" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.documento')) !!}</label>
                        <div class="col-lg-4">
                        	@if($batch->doc)
                        		{{-- Ver documento --}}
                        		<a target="_blank" class="btn btn-xs btn-info" href="{{ asset('docs/batches/'.$batch->doc) }}" rel="tooltip" title="{{ trans('textos.documento_ver') }}">
                        			<i class="fa fa-file"></i>
                        		</a>

                        		{{-- Eliminar documento --}}
                        		<a target="_blank" class="btn btn-xs btn-danger ml-3 btn-confirm" href="{{ route('batches.remove-doc', $batch) }}" rel="tooltip" title="{{ trans('textos.documento_eliminar') }}" data-msg="{{ trans('textos.documento_eliminar_confirm') }}">
                        			<i class="fa fa-trash"></i>
                        		</a>
                        	@else
                            	<x-input-file name="doc"/>
                            @endif
                        </div>
                    </div>

					<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/> 				
				</form>
			</div>
		</div>
	</div>
</div>

@endsection
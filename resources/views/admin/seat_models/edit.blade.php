@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="amortizationId" value="{{ $model->id }}">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Volver a modelos --}}
		<a href="{{ route('seat-models.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_modelos') !!}
		</a>

		@can('seats.create')
			<a class="btn btn-info" href="{{ route('seat-models.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.modelo_nuevo')) !!}</a>
		@endcan

		{{-- Eliminar modelo --}}
		@can('seats.destroy')
			<form method="POST" action="{{ route('seat-models.destroy', $model) }}" class="frm-inline" role="form" accept-charset="UTF-8">
				<input name="_method" type="hidden" value="DELETE">
				{{ csrf_field() }}
				<button type="submit" class="btn btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.modelo_eliminar')) }}" data-msg="{{ ucfirst(trans('textos.modelo_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
			</form>
		@endcan
	</div>
</div>
		
<div class="row mb-3">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<h3 class="mb-3">{!! trans('textos.asiento_contable_modelo') !!} <u>{{ $model->description }}</u></h3>

				{{-- Info --}}
				<div class="mb-3">
					{{-- Fecha alta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.fecha_alta')) !!}: 
						<b>
							@if($model->created_at) {{ $model->created_at->format($languages[session()->get('locale')][4].' H:i:s') }} @endif
						</b>
					</label>

					{{-- Fecha última actualización --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.ultima_edicion')) !!}: 
						<b>
							@if($model->updated_at) {{ $model->updated_at->format($languages[session()->get('locale')][4].' H:i:s') }} @endif
						</b>
					</label>
				</div>

				<form method="post" autocomplete="off" action="{{ route('seat-models.update', $model) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}
					<input type="hidden" name="currency_id" value="{{ $currency->id }}">

					<div class="form-group row">
						{{-- Nombre --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nombre')) !!}*</label>
						<div class="col-lg-4">
							<x-input type="text" name="name" maxlength="255" placeholder="{!! ucfirst(trans('textos.nombre')) !!}" autocomplete="off" required value="{{ $model->name }}"/>
						</div>	
					
						{{-- Diario --}}
						<label for="diary" class="col-lg-1 col-form-label">{!! ucfirst(trans('textos.diario')) !!}</label>
						<div class="col-lg-2">
							<x-select name="diary" class="select2" label="diario_selecciona" :options="$diaries" value="{{ trim($model->diary) }}"/>
						</div>	
					</div>

					<div class="form-group row">
						{{-- Descripción --}}
						<label for="description" class="col-md-2 col-lg-2 col-form-label">{!! ucfirst(trans('textos.descripcion')) !!}</label>
						<div class="col-md-10">
							<x-input-textarea name="description" value="{{ $model->description }}"/>	
						</div>
					</div>
					<br>

					{{-- Añadir líneas de asientos contables --}}
					@include('admin.seats.partials.component-lines', ['title' => trans('textos.asiento_contable_modelo_lineas')])	

					<div id="seatLines">
						@foreach($lines as $line)
							<div class="form-group row">	
								<div class="col-1 text-right line-num" style="padding-top: 7px;">{{ $line->line_num }}.</div>
								<div class="col-md-4"><input type="text" class="form-control" value="{{ $line->account }} - {{ $line->name }}" readonly></div>
								<input type="hidden" name="accounting_account_id[]" value="{{ $line->accounting_account_id }}">
								<div class="col-md-1"><input type="text" class="form-control" value="@if($line->type && isset($seat_lines_types[$line->type])) {!! $seat_lines_types[$line->type] !!} @endif" readonly></div>
								<input type="hidden" name="type[]" value="@if($line->type) {{ $line->type }} @endif">
								<div class="col-md-2"><input type="text" name="debit[]" class="form-control text-right from-lines" value="{{ $line->debit }}" readonly></div>
								<div class="col-md-2"><input type="text" name="credit[]" class="form-control text-right from-lines" value="{{ $line->credit }}" readonly></div>
								<div class="col-1 pt-2"><button class="btn btn-danger btn-circle btn-circle-mini btnRemoveLine"><i class="fa fa-times"></i></button></div>
							</div>
						@endforeach
					</div> 	

					<div class="form-group row" id="seatLinesTotals">
						<div class="col-6">&nbsp;</div>	
						<div class="col-md-2 text-right font-weight-bold pr-4" id="totalDebe"></div>
						<div class="col-md-2 text-right font-weight-bold pr-4" id="totalHaber"></div>
						<div class="col-md-2 font-weight-bold pr-4" id="totalDiff"></div>
					</div>

					<input type="hidden" id="debeTotal" value="@if(isset($debe_total)) {{ $debe_total }} @else 0 @endif">
					<input type="hidden" id="haberTotal" value="@if(isset($haber_total)) {{ $haber_total }} @else 0 @endif">

					<x-btn-submit name="submit_update" id="sbtSeat" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection
@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Volver a asientos contables --}}
		<a href="{{ route('seats.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_asientos_contables') !!}
		</a>

		{{-- Nuevo asiento --}}
		@can('seats.create')
			<a class="btn btn-info" href="{{ route('seats.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.asiento_nuevo')) !!}</a>
		@endcan	

		{{-- Eliminar asiento --}}
		@can('seats.destroy')
			<form method="POST" action="{{ route('seats.destroy', $seat) }}" class="frm-inline" role="form" accept-charset="UTF-8">
				<input name="_method" type="hidden" value="DELETE">
				{{ csrf_field() }}
				<button type="submit" class="btn btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.asiento_eliminar')) }}" data-msg="{{ ucfirst(trans('textos.asiento_eliminar_confirm')) }}"><i class="fa fa-trash"></i></button>
			</form>
		@endcan
	</div>
</div>

<div class="row mb-3">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<h3 class="mb-3">{!! trans('textos.asiento_contable') !!} <u>{{ $seat->id }} - {{ $seat->description }}</u></h3>

				{{-- Info --}}
				<div class="mb-3">
					{{-- Fecha alta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.fecha_alta')) !!}: 
						<b>
							@if($seat->created_at) {{ $seat->created_at->format($languages[session()->get('locale')][4].' H:i:s') }} @endif
						</b>
					</label>

					{{-- Fecha última actualización --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.ultima_edicion')) !!}: 
						<b>
							@if($seat->updated_at) {{ $seat->updated_at->format($languages[session()->get('locale')][4].' H:i:s') }} @endif
						</b>
					</label>

					{{-- Autor --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.creado_por')) !!}: 
						<b>
							{{ $author->name }} {{ $author->surname }}
						</b>
					</label>
				</div>

				<form method="post" autocomplete="off" action="{{ route('seats.update', $seat) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Fecha asiento --}}
						<label for="seat_date" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.fecha')) !!}*</label>
						<div class="col-lg-2">
							@php
							$seat_date = $seat->seat_date != ''? \Carbon\Carbon::parse($seat->seat_date)->format($languages[session()->get('locale')][4]):'';
							@endphp 

							<x-date-picker name="seat_date" :format="$languages[config('app.locale')][4]" required value="{{ $seat_date }}"/>
						</div>	

						{{-- Concepto --}}
						<label for="concept" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.concepto')) !!}*</label>
						<div class="col-lg-6">
							<x-input type="text" name="concept" maxlength="255" placeholder="{!! ucfirst(trans('textos.concepto')) !!}" autocomplete="off" value="{{ $seat->concept }}" required/>
						</div>	
					</div>

					<div class="form-group row">
						{{-- Empresa --}}
						<label for="company_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.empresa')) !!}</label>
						<div class="col-lg-4 position-relative">
							<x-input type="text" name="company_id" id="search_company" placeholder="{!! ucfirst(trans('textos.empresa')) !!}" maxlength="150" value="{{ $company->name }}"/>
						</div>

						<input type="hidden" name="company_id" id="companyId">

						{{-- Diario --}}
						<label for="diary" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.diario')) !!}</label>
						<div class="col-lg-2">
							<x-select name="diary" class="select2" label="diario_selecciona" :options="$diaries" value="{{ $seat->diary }}"/>
						</div>	
					</div>

					<div class="form-group row">
						{{-- Nº documento --}}
						<label for="doc_num" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.documento_num')) !!}</label>
						<div class="col-lg-4 position-relative">
							<x-input type="text" name="doc_num" placeholder="{!! ucfirst(trans('textos.documento_num')) !!}" maxlength="150" value="{{ $seat->doc_num }}"/>
						</div>
					
						{{-- Documento --}}
						<label for="doc" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.documento')) !!}</label>
						<div class="col-lg-4">
							@if($seat->doc)
								@if(file_exists('docs/seats/'.$seat->doc))
									<a target="_blank" class="btn btn-sm btn-info" href="{{ asset('docs/seats/'.$seat->doc) }}" rel="tooltip" title="{{ trans('textos.documento_ver') }}">
										<i class="fa fa-file"></i>
									</a>	
								@else
									<span class="text-warning">{{ trans('textos.documento_no_hallado') }}</span>
								@endif

								{{-- Eliminar documento --}}
								<a class="btn btn-sm btn-danger btn-confirm ml-3" href="{{ route('seats.delete-doc', $seat) }}" rel="tooltip" title="{{ trans('textos.documento_eliminar') }}" data-msg="{{ ucfirst(trans('textos.documento_eliminar_confirm')) }}">
									<i class="fa fa-trash"></i>
								</a>
							@else
								<x-input-file name="doc"/>
							@endif	
						</div>					

						{{-- Serie --}}
						{{-- <label for="serie" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.serie')) !!}</label>
						<div class="col-lg-2">
							<x-input type="text" name="serie" maxlength="15" placeholder="{!! ucfirst(trans('textos.serie')) !!}" autocomplete="off" value="{{ $seat->serie }}"/>
						</div>	 --}}
					</div>

					<div class="form-group row">
						{{-- Descripción --}}
						<label for="description" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.descripcion')) !!}</label>
						<div class="col-9 col-md-10">
							<textarea name="description" class="form-control">{{ $seat->description }}</textarea>
						</div>	
					</div>
					<br>

					{{-- Añadir líneas de asientos contables --}}
					@include('admin.seats.partials.component-lines', ['title' => trans('textos.asiento_contable_lineas')])

					<div class="form-group row mt-4">
						<div class="col-1 text-right"><b>#</b></div>
						<div class="col-md-4 text-center"><b>{{ trans('textos.cuenta_contable') }}</b></div>
						<div class="col-md-1 text-center"><b>{{ ucfirst(trans('textos.tipo')) }}</b></div>
						<div class="col-md-2 text-center"><b>{{ trans('textos.debe') }}</b></div>
						<div class="col-md-2 text-center"><b>{{ trans('textos.haber') }}</b></div>
					</div>		

					<div id="seatLines">
						@foreach($lines as $line)
							<div class="form-group row">	
								<div class="col-1 text-right line-num" style="padding-top: 7px;">{{ $line->line_num }}.</div>
								<div class="col-md-4"><input type="text" class="form-control" value="{{ $line->account }}" readonly></div>
								<input type="hidden" name="accounting_account_id[]" value="{{ $line->accounting_account_id }}">
								<div class="col-md-1"><input type="text" class="form-control" value="@if($line->type && isset($seat_lines_types[$line->type])) {!! $seat_lines_types[$line->type] !!} @endif" readonly></div>
								<input type="hidden" name="type[]" value="@if($line->type) {{ $line->type }} @endif">
								<div class="col-md-2"><input type="text" name="debit[]" class="form-control text-right from-lines" value="{{ $line->debit }}" readonly></div>
								<div class="col-md-2"><input type="text" name="credit[]" class="form-control text-right from-lines" value="{{ $line->credit }}" readonly></div>
								<div class="col-1 pt-2"><button class="btn btn-danger btn-circle btn-circle-mini btnRemoveLine"><i class="fa fa-times"></i></button></div>
							</div>
						@endforeach
					</div> 

					<x-btn-submit name="submit_update" id="sbtSeat" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>	
	</div>
</div>

@endsection
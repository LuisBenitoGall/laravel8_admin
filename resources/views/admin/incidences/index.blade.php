@extends('admin.partials.layout')

@section('content')

@php
$customer_id = isset($customer) && $customer? $customer->id:'';
$subject_id = isset($subject) && $subject? $subject->id:'';
$from_ = isset($from) && $from != ''? \Carbon\Carbon::parse($from)->format($languages[session()->get('locale')][4]):'';
$to_ = isset($to) && $to != ''? \Carbon\Carbon::parse($to)->format($languages[session()->get('locale')][4]):'';
$status_ = isset($status) && $status? $status:'';
@endphp

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Abrir nueva incidencia --}}
		<a href="{{ route('incidences.create') }}" class="btn btn-info">
			<i class="fa fa-plus"></i>
			{!! ucfirst(trans('textos.incidencia_nueva')) !!}
		</a>

		{{-- Borrar filtros --}}
		@if($customer_id != '' || $subject_id != '' || $from_ != '' || $to_ != '' || $status_ != '')
			<a href="{{ route('incidences.index') }}" class="btn btn-info">
				<i class="fa fa-undo-alt"></i>
				{!! ucfirst(trans('textos.filtros_quitar')) !!}
			</a>
		@endif
	</div>
</div>

{{-- Filtros --}}
<div class="row">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('incidences.list') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						<div class="col-md-10 offset-md-2">
							<p class="text-warning">{!! trans('textos.incidencias_filtro') !!}</p>
						</div>

						{{-- Selector de cliente --}}
						<label for="customer" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.cliente')) !!}</label>
			            <div class="col-md-4 position-relative">
			                <x-input type="text" name="customer" id="search_customer" placeholder="{!! ucfirst(trans('textos.cliente')) !!}" maxlength="150"/>

			                <input type="hidden" name="customer_id" id="customerId">
			            </div>			            

			            {{-- Selector usuario --}}
                        <label for="subject" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.usuarios')) !!}</label>
                        <div class="col-md-4">
							<x-input type="text" name="subject" id="search_user" placeholder="{!! ucfirst(trans('textos.usuario')) !!}" maxlength="150"/>

							<input type="hidden" name="subject_id" id="userId">
                        </div>
					</div>

					<div class="form-group row">
						{{-- Desde --}}
						<label for="from" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.desde')) !!}</label>
						<div class="col-lg-2">
							<x-date-picker name="from" value="{{ $from_ }}" :format="$languages[config('app.locale')][4]"/>	
						</div>

						{{-- Hasta --}}
						<label for="to" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.hasta')) !!}</label>
						<div class="col-lg-2">
							<x-date-picker name="to" value="{{ $to_ }}" :format="$languages[config('app.locale')][4]"/>	
						</div>
					</div>

					<div class="form-group row">
						{{-- Incidencia --}}
						<label for="incidence_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.incidencia_num')) !!} </label>
						<div class="col-lg-2">
							<input class="form-control" type="text" name="incidence_id" maxlength="25">
						</div>

						{{-- Estado --}}
						<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.estado')) !!} </label>
						<div class="col-lg-2">
							<x-select name="status" class="select2" :options="$incidence_status" label="estado_selec" value="{{ $status_ }}"/>
						</div>
					</div>

					<x-btn-submit name="submit_save" label="{{ trans('textos.buscar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

{{-- Tabla --}}
<div class="row">
	@if(isset($customer) && $customer)
		<div class="col-12 mb-3">
			<h3>
				{{ trans('textos.incidencias') }}
				<u>{{ $customer->name }}</u>
			</h3>
		</div>
	@endif

	@if(isset($subject) && $subject)
		<div class="col-12 mb-3">
			<h3>
				{{ trans('textos.incidencias') }}
				<u>{{ $subject->name }} {{ $subject->surname }}</u>
			</h3>
		</div>
	@endif

	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblIncidences">
			<thead>
				<tr>
					<th class="text-center wdt50">#</th>
					<th class="wdt150">{{ ucfirst(trans('textos.concepto')) }}</th>
					<th class="wdt150">{{ ucfirst(trans('textos.cliente')) }}</th>
					<th class="text-center wdt100">{{ ucfirst(trans('textos.fecha')) }}</th>
					<th class="wdt100">{{ ucfirst(trans('textos.estado')) }}</th>
					<th class="text-center wdt50">{{ ucfirst(trans('textos.acciones')) }}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Incidences:
    if($('#tblIncidences').length > 0){
        var customer_id = '{{ $customer_id }}';
        var subject_id = '{{ $subject_id }}';
        var from = '{{ $from_ }}';
        var to = '{{ $to_ }}';
        var status = '{{ $status_ }}';

        $('#tblIncidences').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                type: "POST",
                url: "/api/admin/incidences",
                dataType: "json",
                data: {
                	_token: $('meta[name="csrf-token"]').attr('content'),
                	customer_id: customer_id,
                	subject_id: subject_id,
                	from: from,
                	to: to,
                	status: status	
                },
                error: function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'num', className: 'text-right'},
                {data: 'concept'},
                {data: 'customer'},
                {data: 'date', className: 'text-right'},
                {data: 'status'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }
});
</script>
@endpush

@endsection
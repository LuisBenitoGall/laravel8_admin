@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		<a href="{{ route('company-accounts.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_mi_cuenta') !!}
		</a>
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-4">{{ ucfirst(trans('textos.cuenta_renovar')) }} <u>{{ $company->name }}</u></h3>	

				<form method="post" autocomplete="off" action="{{ route('company-accounts.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}
					<input type="hidden" name="account" id="account">

					<div class="row">
						@foreach($accounts as $row)
							<div class="col-xs-6 col-md-4 col-lg-3"> 
								{{-- Price Table Item --}}
								<div class="panel panel-warning account">
									<div class="panel-heading">
										{{ ucfirst($row->name) }}
									</div>

									<div class="panel-body">
										<h2 class="account-price">
											@if($currency)
												@php
												$cur = $currency->symbol;
												@endphp
											@else
												@php
												$cur = config('constants.CURRENCY_');
												@endphp
											@endif
											{{ $row->rate }}{{ $cur }}
										</h2>

										<p>{{ $row->description }}</p>

										<button type="button" class="btn btn-outline btn-primary account-btn" data-id="{{ $row->id }}">
											{{ ucfirst(trans('textos.seleccionar')) }}
										</button>
									</div>
								</div>
							</div>
						@endforeach
					</div>

					<x-input-errors name="account"/>

					<x-btn-submit name="submit_save" label="{{ trans('textos.continuar') }}"/>	
				</form>
			</div>
		</div>
	</div>
</div>

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet"> 
@endpush

@push('scripts')
<script>
$(function(){
	//Seleccionar tarifa:
	$('.account-btn').on('click', function(){
		var id = $(this).data('id');
		$('.account-btn').text(txtSeleccionar);
		$('#account').val(id);
		$(this).html(txtSeleccionado+'<i class="fa fa-check ml-1"></i>');
	});
});
</script>
@endpush

@endsection
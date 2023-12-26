@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-sm-12">
		<a href="{{ route('remittances.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_remesas') !!}
		</a>

		{{-- Nueva remesa --}}
		@can('cash.create')
			<a class="btn btn-info" href="{{ route('remittances.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.remesa_nueva')) !!}</a>
		@endcan
	</div>
</div>

<div class="row mb-3">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<h3 class="mb-3">
					{!! ucfirst(trans('textos.remesa')) !!} 
					<u>{{ $remittance->remittance_num }}</u>
				</h3>	

				{{-- Info --}}
				<div class="mb-4">
					{{-- Autor --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.autor')) !!}: <b>{{ $author->name }} {{ $author->surname }}</b>
					</label>

					{{-- Fecha alta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.fecha_alta')) !!}: 
						<b>
							{{ $remittance->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Fecha última actualización --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.ultima_edicion')) !!}: 
						<b>
							{{ $remittance->updated_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>
				</div>

				<form method="post" autocomplete="off" action="{{ route('remittances.update', $remittance->id) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Cuenta bancaria --}}
						<label for="bank_account" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.bank_account')) !!}*</label>
						<div class="col-lg-4">
							<x-select name="bank_account" class="select2" :options="$bank_accounts" label="cuenta_selec" value="{{ $remittance->bank_account_id }}" required/>
						</div>
					</div>

					<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/> 				
				</form>
			</div>
		</div>
	</div>
</div>

@endsection
@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('deliveries.index')
			<a href="{{ route('deliveries.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_entregas') !!}
			</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('deliveries.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}
						
					{{-- Selecciona cliente --}}
					<div class="form-group row">
						<label for="company_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cliente')) !!} *</label>
						<div class="col-lg-8">
							<x-select name="company_id" class="select2" label="cliente_selec" :options="$companies" required/>
						</div>
					</div>

					<x-btn-submit name="submit_save" classdiv="col-sm-10 text-right" label="{{ trans('textos.continuar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection
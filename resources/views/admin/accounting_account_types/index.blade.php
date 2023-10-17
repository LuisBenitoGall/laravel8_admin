@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		@can('accounting-account-types.create')
			<a class="btn btn-info" href="{{ route('accounting-account-types.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.grupo_nuevo')) !!}</a>
		@endcan
	</div>
</div>

<div class="row mb-3">
	<div class="col-12 mb-3">
		<h3>
			{!! trans('textos.grupos_contables') !!} 
		</h3>
	</div>

	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblAccountingAccountTypes">
			<thead>
				<tr>
					<th class="wdt50">{!! ucfirst(trans('textos.codigo')) !!}</th>
					<th class="wdt150">{!! ucfirst(trans('textos.nombre')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@endsection
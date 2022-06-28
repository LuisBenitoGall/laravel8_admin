@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones --}}
	<div class="col-12">
		@can('accounts.create')
			<a class="btn btn-info" href="{{ route('accounts.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.cuenta_nueva')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblAccounts">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.cuenta')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.tarifa')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.duracion')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@endsection
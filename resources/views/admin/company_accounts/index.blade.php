@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('companies.edit')
			<a href="{{ route('company-accounts.create') }}" class="btn btn-info"><i class="fa fa-shopping-cart"></i> {!! ucfirst(trans('textos.cuenta_renovar')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblCompanyAccounts">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.cuenta')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.inicio')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fin')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.precio')) !!}</th>
					<th>{!! ucfirst(trans('textos.estado')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@endsection
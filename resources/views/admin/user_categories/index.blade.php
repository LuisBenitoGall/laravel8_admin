@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		<a class="btn btn-info" href="{{ route('user-categories.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.categoria_nueva')) !!}</a>
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblUserCategories">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.categoria')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@endsection
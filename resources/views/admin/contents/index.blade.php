@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		<a class="btn btn-info" href="{{ route('contents.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.contenido_nuevo')) !!}</a>
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblContents">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.contenido')) !!}</th>
					<th>{!! ucfirst(trans('textos.codigo')) !!}</th>
					<th>{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@endsection
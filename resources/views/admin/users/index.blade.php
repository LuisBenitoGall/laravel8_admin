@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="all" value="{{ $all }}">

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		{{-- Toggle vista - cuadrícula --}}
        @include('admin.partials.toggle-views', ['cookie' => 'users'])

        {{-- Nuevo usuario --}}
		@can('users.create')
			<a href="{{ route('users.create') }}" class="btn btn-info"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.usuario_nuevo')) !!}</a>
		@endcan
	</div>
</div>

{{-- Vista listado --}}
<div class="row">
	<div class="table-responsive toggle-view-list">
		<table class="table table-hover dtabla" id="tblUsers">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.usuario')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
					<th>Email</th>
					<th>{!! ucfirst(trans('textos.telefonos')) !!}</th>
					<th>{!! ucfirst(trans('textos.categorias')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.imagen')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

{{-- Vista cuadrícula --}}
<div class="toggle-view-grid">
	{{-- Componente Users Grid --}}
	@livewire('users-grid')

	@include('admin.partials.back-to-top')

	{{-- <div class="row"> --}}
		{{-- @foreach($users as $row)
			<div class="col-sm-12 col-md-6 col-lg-3 mb-3">
				<div class="user">
					<h5>
						@can('users.destroy')
							<a href="{{ route('users.edit', $row->id) }}">{{ $row->name }} {{ $row->surname }}</a>
						@else
							<label>{{ $row->name }} {{ $row->surname }}</label>
						@endcan
					</h5>

					<div class="user-image">
						@if($row->Image)
							<img src="{!! asset('img/users/'.$row->Image) !!}" class="img-fluid" alt="{{ $row->name }} {{ $row->surname }}">
						@else
							<div class="product-image-default">
								<i class="fa fa-user"></i>
							</div>
						@endif	
					</div>	

					<div class="user-data">
						{{ ucfirst(trans('textos.fecha_alta')) }}: <b>{{ $row->created_at->format(config('locale.languages.'.config('app.locale'))[4]) }}</b>

						@can('users.destroy')
							<a href="{{ route('users.edit', $row->id) }}" class="pull-right">
								<i class="fa fa-edit" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"></i>
							</a>
						@endcan
						<br>

						Email: <b>{{ $row->email }}</b>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		@endforeach --}}
	{{-- </div> --}}
</div>

@endsection